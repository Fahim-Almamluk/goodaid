<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Http\Requests\BatchFilterRequest;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    /**
     * Session key for storing filters
     */
    const FILTER_SESSION_KEY = 'batches.filters';
    const LAST_PAGE_SESSION_KEY = 'last_visited_page';

    /**
     * Apply filters via POST and redirect back
     */
    public function applyFilters(BatchFilterRequest $request)
    {
        $filters = $request->getFilters();
        session()->put(self::FILTER_SESSION_KEY, $filters);
        
        return redirect()->route('batches.index');
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        session()->forget(self::FILTER_SESSION_KEY);
        
        return redirect()->route('batches.index');
    }

    /**
     * Get filters from session
     */
    protected function getFiltersFromSession(): array
    {
        return session()->get(self::FILTER_SESSION_KEY, []);
    }

    /**
     * Clear filters if user navigated away and came back
     */
    protected function clearFiltersIfNavigatedAway(): void
    {
        $lastPage = session(self::LAST_PAGE_SESSION_KEY);
        $currentPage = 'batches';
        
        // If last visited page was different, clear filters
        if ($lastPage && $lastPage !== $currentPage) {
            session()->forget(self::FILTER_SESSION_KEY);
        }
        
        // Update last visited page
        session()->put(self::LAST_PAGE_SESSION_KEY, $currentPage);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Clear filters if user navigated away and came back
        $this->clearFiltersIfNavigatedAway();
        
        // Get filters from session
        $filters = $this->getFiltersFromSession();
        
        $query = Batch::query();
        
        // البحث
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        // فلتر الحالة
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Pagination - عرض عدد النتائج لكل صفحة (افتراضي 100)
        $perPage = (int) ($filters['per_page'] ?? 100);
        // السماح بقيم محدودة فقط لتحسين الأداء
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 100;
        }

        // Include total beneficiaries and received beneficiaries counts
        $batches = $query->withCount('beneficiaries')
            ->withCount(['beneficiaries as received_count' => function($q) {
                $q->where('batch_recipients.received', 1);
            }])
            ->latest()->paginate($perPage);
        
        // Set custom pagination path without query string
        $batches->setPath(route('batches.index'));
        
        return view('batches.index', compact('batches', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('batches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:food,health,clothes',
            'quantity' => 'required|integer|min:1',
            'batch_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $batch = Batch::create($validated);

        return redirect()->route('batches.manage', $batch)
            ->with('success', 'تم إنشاء الطرد بنجاح');
    }

    /**
     * Display the manage page for a batch.
     */
    public function manage(Request $request, Batch $batch)
    {
        // جلب المستفيدين من الطرد مع pagination
        $batchBeneficiaries = $batch->beneficiaries()
            ->withCount('familyMembers')
            ->paginate(100)
            ->withQueryString();
        
        $beneficiaries = \App\Models\Beneficiary::active()->get();
        return view('batches.manage', compact('batch', 'beneficiaries', 'batchBeneficiaries'));
    }

    /**
     * Display the distribution page for a batch.
     */
    public function distribution(Request $request, Batch $batch)
    {
        // Pagination - عرض عدد النتائج لكل صفحة (افتراضي 100)
        $perPage = (int) $request->get('per_page', 100);
        // السماح بقيم محدودة فقط لتحسين الأداء
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 100;
        }

        $beneficiaries = $batch->beneficiaries()
            ->withCount('familyMembers')
            ->paginate($perPage)
            ->withQueryString();

        return view('batches.distribution', compact('batch', 'beneficiaries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Batch $batch)
    {
        if ($batch->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'لا يمكن تعديل طرد تم اعتماده');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:food,health,clothes',
            'quantity' => 'required|integer|min:' . $batch->beneficiaries->count(),
            'batch_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->back()
            ->with('success', 'تم تحديث معلومات الطرد بنجاح');
    }

    /**
     * Approve a batch.
     */
    public function approve(Batch $batch)
    {
        if ($batch->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'الطرد معتمد بالفعل');
        }

        if ($batch->beneficiaries->count() === 0) {
            return redirect()->back()
                ->with('error', 'لا يمكن اعتماد طرد بدون مستفيدين');
        }

        $batch->update(['status' => 'active']);

        return redirect()->route('batches.distribution', $batch)
            ->with('success', 'تم اعتماد الطرد بنجاح');
    }

    /**
     * Add beneficiaries to a batch.
     */
    public function addBeneficiaries(Request $request, Batch $batch)
    {
        if ($batch->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إضافة مستفيدين لطرد معتمد'
            ], 403);
        }

        $validated = $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        $selectedIds = $validated['beneficiary_ids'];
        $currentCount = $batch->beneficiaries->count();
        $quantity = $batch->quantity ?? 0;

        if ($quantity > 0 && ($currentCount + count($selectedIds)) > $quantity) {
            return response()->json([
                'success' => false,
                'message' => "عدد المستفيدين المحدد ({$currentCount} + " . count($selectedIds) . ") يتجاوز الكمية المحددة ({$quantity})"
            ], 400);
        }

        // إضافة المستفيدين (تجاهل المكررين)
        $newIds = array_diff($selectedIds, $batch->beneficiaries->pluck('id')->toArray());
        $batch->beneficiaries()->attach($newIds);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المستفيدين بنجاح',
            'added' => count($newIds)
        ]);
    }

    /**
     * Remove a beneficiary from a batch.
     */
    public function removeBeneficiary(Batch $batch, $beneficiaryId)
    {
        if ($batch->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إزالة مستفيد من طرد معتمد'
            ], 403);
        }

        $batch->beneficiaries()->detach($beneficiaryId);
        
        return response()->json([
            'success' => true,
            'message' => 'تم إزالة المستفيد بنجاح'
        ]);
    }

    /**
     * Preview Excel import.
     */
    public function importExcelPreview(Request $request, Batch $batch)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xls,xlsx|max:10240',
        ]);

        try {
            if (!extension_loaded('zip')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ملحق zip غير مفعّل في PHP. يرجى تفعيله لاستيراد ملفات Excel.'
                ], 400);
            }

            $filePath = $request->file('excel_file')->getRealPath();
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
            
            // قراءة الصف الأول للحصول على أسماء الأعمدة
            $headerRow = [];
            $highestColumn = $sheet->getHighestColumn();
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headerRow[$col] = trim($sheet->getCell($col . '1')->getValue());
            }
            
            // البحث عن عمود رقم الهوية
            $nationalIdColumn = null;
            foreach ($headerRow as $col => $header) {
                $header = strtolower(trim($header));
                if (in_array($header, ['national_id', 'nationalid', 'رقم_الهوية', 'رقم الهوية', 'national id', 'رقم الهوية الوطنية', 'id'])) {
                    $nationalIdColumn = $col;
                    break;
                }
            }
            
            if (!$nationalIdColumn) {
                $nationalIdColumn = 'A';
            }
            
            $rows = [];
            $matchesCount = 0;
            $notFoundCount = 0;
            $duplicatesCount = 0;
            $foundIds = [];
            
            for ($row = 2; $row <= $highestRow; $row++) {
                $cellValue = $sheet->getCell($nationalIdColumn . $row)->getValue();
                
                if ($cellValue instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
                    $nationalId = trim($cellValue->getPlainText());
                } else {
                    $nationalId = trim((string)$cellValue);
                }
                
                if (empty($nationalId)) {
                    continue;
                }
                
                $nationalId = preg_replace('/[^0-9]/', '', $nationalId);
                
                if (empty($nationalId)) {
                    continue;
                }

                // التحقق من التكرار
                if (in_array($nationalId, $foundIds)) {
                    $rows[] = [
                        'national_id' => $nationalId,
                        'status' => 'duplicate',
                        'is_duplicate' => true,
                        'exists_in_batch' => false,
                    ];
                    $duplicatesCount++;
                    continue;
                }
                
                $foundIds[] = $nationalId;
                
                // البحث عن المستفيد
                $beneficiary = \App\Models\Beneficiary::where('national_id', $nationalId)->first();
                
                if ($beneficiary) {
                    $existsInBatch = $batch->beneficiaries->contains($beneficiary->id);
                    $rows[] = [
                        'national_id' => $nationalId,
                        'beneficiary_id' => $beneficiary->id,
                        'beneficiary_name' => $beneficiary->name,
                        'beneficiary_phone' => $beneficiary->phone,
                        'status' => 'found',
                        'is_duplicate' => false,
                        'exists_in_batch' => $existsInBatch,
                    ];
                    if (!$existsInBatch) {
                        $matchesCount++;
                    }
                } else {
                    $rows[] = [
                        'national_id' => $nationalId,
                        'status' => 'not_found',
                        'is_duplicate' => false,
                        'exists_in_batch' => false,
                    ];
                    $notFoundCount++;
                }
            }
            
            return response()->json([
                'success' => true,
                'rows' => $rows,
                'total_rows' => count($rows),
                'matches_count' => $matchesCount,
                'not_found_count' => $notFoundCount,
                'duplicates_count' => $duplicatesCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في قراءة ملف Excel: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Confirm Excel import and add beneficiaries.
     */
    public function confirmExcelImport(Request $request, Batch $batch)
    {
        if ($batch->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إضافة مستفيدين لطرد معتمد'
            ], 403);
        }

        $validated = $request->validate([
            'beneficiary_ids' => 'required|array',
            'beneficiary_ids.*' => 'exists:beneficiaries,id',
        ]);

        $selectedIds = $validated['beneficiary_ids'];
        $currentCount = $batch->beneficiaries->count();
        $quantity = $batch->quantity ?? 0;

        if ($quantity > 0 && ($currentCount + count($selectedIds)) > $quantity) {
            return response()->json([
                'success' => false,
                'message' => "عدد المستفيدين المحدد ({$currentCount} + " . count($selectedIds) . ") يتجاوز الكمية المحددة ({$quantity})"
            ], 400);
        }

        // إضافة المستفيدين (تجاهل المكررين)
        $newIds = array_diff($selectedIds, $batch->beneficiaries->pluck('id')->toArray());
        $batch->beneficiaries()->attach($newIds);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المستفيدين بنجاح',
            'added' => count($newIds)
        ]);
    }

    /**
     * Toggle received status for a batch recipient.
     */
    public function toggleReceived(Request $request, Batch $batch, $beneficiaryId)
    {
        $validated = $request->validate([
            'received' => 'required|boolean',
        ]);

        $batch->beneficiaries()->updateExistingPivot($beneficiaryId, [
            'received' => $validated['received'],
            'received_at' => $validated['received'] ? now() : null,
            'approved_by' => $validated['received'] ? auth()->id() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $validated['received'] ? 'تم تسجيل الاستلام بنجاح' : 'تم إلغاء الاستلام بنجاح'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Batch $batch)
    {
        if ($batch->status === 'active') {
            return redirect()->back()
                ->with('error', 'لا يمكن حذف طرد معتمد');
        }

        $batch->beneficiaries()->detach();
        $batch->delete();

        return redirect()->route('batches.index')
            ->with('success', 'تم حذف الطرد بنجاح');
    }

    /**
     * Export batches to Excel
     */
    public function exportExcel(Request $request)
    {
        // Get filters from session (same as index)
        $filters = $this->getFiltersFromSession();
        
        $query = Batch::query();

        // تطبيق نفس الفلاتر من index
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $batches = $query->withCount('beneficiaries')
            ->withCount(['beneficiaries as received_count' => function($q) {
                $q->where('batch_recipients.received', 1);
            }])
            ->latest()->get();

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('الطرود');
            
            // العناوين
            $headers = [
                'اسم الطرد',
                'النوع',
                'الكمية',
                'عدد المستفيدين',
                'تم الاستلام',
                'لم يستلم',
                'الحالة',
                'التاريخ',
            ];
            
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $col++;
            }
            
            // البيانات
            $row = 2;
            foreach ($batches as $batch) {
                $type = match($batch->type) {
                    'food' => 'غذائي',
                    'health' => 'صحي',
                    'clothes' => 'ملابس',
                    default => 'غير محدد',
                };
                
                $status = $batch->status === 'draft' ? 'جديد' : 'معتمد';
                $notReceivedCount = $batch->beneficiaries_count - $batch->received_count;
                
                $data = [
                    $batch->name,
                    $type,
                    $batch->quantity ?? '-',
                    $batch->beneficiaries_count,
                    $batch->received_count,
                    $notReceivedCount,
                    $status,
                    $batch->created_at->format('Y-m-d'),
                ];
                
                $col = 'A';
                foreach ($data as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }
            
            // ضبط عرض الأعمدة
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $filename = 'batches-' . date('Y-m-d') . '.xlsx';
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('batches.index')
                ->with('error', 'حدث خطأ أثناء تصدير Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export batch distribution to Excel
     */
    public function exportDistributionExcel(Request $request, Batch $batch)
    {
        $beneficiaries = $batch->beneficiaries()
            ->withCount('familyMembers')
            ->get();

        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('مستفيدي الطرد');
            
            // العناوين
            $headers = [
                'الاسم',
                'رقم الهوية',
                'الهاتف',
                'عدد الأفراد',
                'حالة الاستلام',
                'تاريخ الاستلام',
                'المدخل',
            ];
            
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '1', $header);
                $sheet->getStyle($col . '1')->getFont()->setBold(true);
                $col++;
            }
            
            // البيانات
            $row = 2;
            foreach ($beneficiaries as $beneficiary) {
                $pivot = $beneficiary->pivot;
                $received = (bool)($pivot->received ?? false);
                $receivedAt = $pivot->received_at ?? null;
                $approvedBy = $pivot->approved_by ?? null;
                
                $receivedStatus = $received ? 'تم الاستلام' : 'لم يستلم';
                $receivedDate = $receivedAt ? \Carbon\Carbon::parse($receivedAt)->format('Y-m-d H:i') : '-';
                $approvedByName = $approvedBy ? (\App\Models\User::find($approvedBy)->name ?? '-') : '-';
                
                $data = [
                    $beneficiary->name,
                    $beneficiary->national_id ?? '-',
                    $beneficiary->phone ?? '-',
                    $beneficiary->family_members_count + 1,
                    $receivedStatus,
                    $receivedDate,
                    $approvedByName,
                ];
                
                $col = 'A';
                foreach ($data as $value) {
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }
            
            // ضبط عرض الأعمدة
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            $filename = 'batch-distribution-' . $batch->name . '-' . date('Y-m-d') . '.xlsx';
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
            $writer->save($tempFile);
            
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('batches.distribution', $batch)
                ->with('error', 'حدث خطأ أثناء تصدير Excel: ' . $e->getMessage());
        }
    }
}

