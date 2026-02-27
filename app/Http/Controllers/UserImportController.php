<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserImportController extends Controller
{
    /**
     * Handle Excel import
     */
    public function import(Request $request)
    {
        try {
            $request->validate([
                'excel_file' => 'required|file|mimes:xlsx,xls,csv',
                'role' => 'required|in:admin,user',
            ]);

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Skip header row
            $dataRows = array_slice($rows, 1);

            $imported = 0;
            $errors = [];
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

                    // Check if user with same ID number already exists
                    if (User::where('id_number', $idNumber)->exists()) {
                        $errorDetails[] = "الصف {$rowNumber}: رقم الهوية {$idNumber} موجود بالفعل";
                        continue;
                    }

                    // Check if user with same phone already exists
                    if (User::where('phone', $phone)->exists()) {
                        $errorDetails[] = "الصف {$rowNumber}: رقم الجوال {$phone} موجود بالفعل";
                        continue;
                    }

                    // Generate username from name and id number
                    $username = strtolower(str_replace(' ', '_', $name)) . '_' . substr($idNumber, -4);
                    
                    // Ensure unique username
                    $originalUsername = $username;
                    $counter = 1;
                    while (User::where('username', $username)->exists()) {
                        $username = $originalUsername . '_' . $counter;
                        $counter++;
                    }

                    // Generate random password
                    $password = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

                    // Create user
                    User::create([
                        'name' => $name,
                        'id_number' => $idNumber,
                        'phone' => $phone,
                        'username' => $username,
                        'email' => null,
                        'password' => Hash::make($password),
                        'role' => $request->role,
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
                'message' => "تم استيراد {$imported} مستخدم بنجاح",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في معالجة الملف: ' . $e->getMessage(),
            ], 400);
        }
    }

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
}
