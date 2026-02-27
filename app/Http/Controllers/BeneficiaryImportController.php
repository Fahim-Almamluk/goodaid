<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BeneficiaryImportController extends Controller
{
    /**
     * Preview Excel import
     */
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row and get all rows for preview
            $previewRows = array_slice($rows, 1);
            
            // Filter out completely empty rows
            $previewRows = array_filter($previewRows, function($row) {
                return !empty($row[0]) || !empty($row[1]) || !empty($row[2]);
            });

            return response()->json([
                'success' => true,
                'preview' => array_values($previewRows), // Re-index array
                'totalRows' => count($previewRows),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في قراءة الملف: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Handle Excel import
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            $imported = 0;
            $errorDetails = [];

            foreach ($dataRows as $index => $row) {
                // Skip empty rows
                if (empty($row[0]) && empty($row[1]) && empty($row[2])) {
                    continue;
                }

                $rowNumber = $index + 2; // +2 because we skipped header and array is 0-indexed

                try {
                    $name = isset($row[0]) ? trim($row[0]) : null;
                    $idNumber = isset($row[1]) ? trim($row[1]) : null;
                    $phone = isset($row[2]) ? trim($row[2]) : null;

                    // Validation
                    if (!$name) {
                        $errorDetails[] = "الصف {$rowNumber}: الاسم مفقود";
                        continue;
                    }

                    if (!$idNumber) {
                        $errorDetails[] = "الصف {$rowNumber}: رقم الهوية مفقود";
                        continue;
                    }

                    if (!$phone) {
                        $errorDetails[] = "الصف {$rowNumber}: رقم الجوال مفقود";
                        continue;
                    }

                    // Check if beneficiary with same ID number already exists
                    if (Beneficiary::where('national_id', $idNumber)->exists()) {
                        $errorDetails[] = "الصف {$rowNumber}: رقم الهوية {$idNumber} موجود بالفعل";
                        continue;
                    }

                    // Generate username from name
                    $username = $this->generateUsername($name, $idNumber);
                    
                    // Ensure unique username
                    $originalUsername = $username;
                    $counter = 1;
                    while (Beneficiary::where('username', $username)->exists()) {
                        $username = $originalUsername . $counter;
                        $counter++;
                    }

                    // Create beneficiary (without password - will be set on first login)
                    Beneficiary::create([
                        'name' => $name,
                        'national_id' => $idNumber,
                        'phone' => $phone,
                        'username' => $username,
                        'password' => null,
                        'has_set_password' => false,
                        'is_active' => true,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errorDetails[] = "الصف {$rowNumber}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'imported' => $imported,
                'errors' => $errorDetails,
                'message' => "تم استيراد {$imported} مستفيد بنجاح",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في معالجة الملف: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate username from Arabic name
     * Example: "محمد أحمد" -> "mahmad" (first letter of first name + last name)
     */
    private function generateUsername($name, $idNumber)
    {
        // Remove extra spaces
        $name = trim(preg_replace('/\s+/', ' ', $name));
        
        // Split name into parts
        $nameParts = explode(' ', $name);
        
        if (count($nameParts) >= 2) {
            // First letter of first name + last name
            $firstLetter = mb_substr($nameParts[0], 0, 1, 'UTF-8');
            $lastName = $nameParts[count($nameParts) - 1];
            
            // Convert Arabic to English transliteration
            $firstLetterEn = $this->arabicToEnglish($firstLetter);
            $lastNameEn = $this->arabicToEnglish($lastName);
            
            $username = strtolower($firstLetterEn . $lastNameEn);
        } else {
            // If only one name, use first 3 letters
            $username = $this->arabicToEnglish($name);
            $username = strtolower(mb_substr($username, 0, 5, 'UTF-8'));
        }
        
        // Add last 4 digits of ID for uniqueness
        $username .= substr($idNumber, -4);
        
        // Remove special characters, keep only alphanumeric
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        
        return $username;
    }

    /**
     * Simple Arabic to English transliteration
     */
    private function arabicToEnglish($text)
    {
        $map = [
            'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'aa',
            'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
            'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'th',
            'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'z',
            'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a',
            'ة' => 'h', 'ئ' => 'y', 'ء' => 'a',
        ];
        
        $result = '';
        $text = mb_strtolower($text, 'UTF-8');
        
        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $result .= $map[$char] ?? $char;
        }
        
        return $result;
    }
}
