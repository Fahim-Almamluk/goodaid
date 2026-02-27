<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\FamilyMember;
use App\Http\Requests\BeneficiaryFilterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BeneficiaryController extends Controller
{
    /**
     * Session key for storing filters
     */
    const FILTER_SESSION_KEY = 'beneficiaries.filters';
    const LAST_PAGE_SESSION_KEY = 'last_visited_page';

    /**
     * Apply filters via POST and redirect back
     */
    public function applyFilters(BeneficiaryFilterRequest $request)
    {
        $filters = $request->getFilters();
        session()->put(self::FILTER_SESSION_KEY, $filters);
        
        return redirect()->route('beneficiaries.index');
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        session()->forget(self::FILTER_SESSION_KEY);
        
        return redirect()->route('beneficiaries.index');
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
        $currentPage = 'beneficiaries';
        
        // If last visited page was different, clear filters
        if ($lastPage && $lastPage !== $currentPage) {
            session()->forget(self::FILTER_SESSION_KEY);
        }
        
        // Update last visited page
        session()->put(self::LAST_PAGE_SESSION_KEY, $currentPage);
    }

    public function index(Request $request)
    {
        // Clear filters if user navigated away and came back
        $this->clearFiltersIfNavigatedAway();
        
        // Get filters from session
        $filters = $this->getFiltersFromSession();
        
        // If page parameter exists in URL, keep it
        $page = $request->get('page', 1);
        
        // If batch_id is in URL, add to filters temporarily
        if ($request->filled('batch_id')) {
            $filters['batch_id'] = $request->batch_id;
        }

        $query = Beneficiary::query();

        // البحث المتقدم - في حقول متعددة
        if (!empty($filters['beneficiary_id'])) {
            $query->where('id', $filters['beneficiary_id']);
        } elseif (!empty($filters['search'])) {
            // البحث النصي المحسّن
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('familyMembers', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('national_id', 'like', "%{$search}%");
                  });
            });
        }

        // فلتر الحالة السكنية
        if (!empty($filters['residence_status'])) {
            $query->where('residence_status', $filters['residence_status']);
        }

        // فلتر العلاقة - البحث في القيم الجديدة والقديمة
        if (!empty($filters['relationship'])) {
            $relationshipValues = Beneficiary::getRelationshipValuesForSearch($filters['relationship']);
            $query->whereIn('relationship', $relationshipValues);
        }

        // فلتر الحالة (موجود/غير موجود)
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // فلتر الحوامل
        if (!empty($filters['has_pregnant'])) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_pregnant', true);
            });
        }

        // فلتر المرضعات
        if (!empty($filters['has_nursing'])) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_nursing', true);
            });
        }

        // فلتر الأطفال (أقل من 18 سنة) - يعتمد على تاريخ الميلاد
        if (!empty($filters['has_children'])) {
            $eighteenYearsAgo = Carbon::now()->subYears(18);
            $query->whereHas('familyMembers', function($q) use ($eighteenYearsAgo) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>', $eighteenYearsAgo->format('Y-m-d'));
            });
        }

        // فلتر العمر (أكبر من عمر معين)
        if (!empty($filters['age_min'])) {
            $minDate = Carbon::now()->subYears($filters['age_min'])->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($minDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '<=', $minDate);
            });
        }

        // فلتر العمر (أصغر من عمر معين)
        if (!empty($filters['age_max'])) {
            $maxDate = Carbon::now()->subYears($filters['age_max'])->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($maxDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>=', $maxDate);
            });
        }

        // فلتر عدد الأفراد - الحد الأدنى (يستخدم حقل number_of_members المخزن)
        if (!empty($filters['members_min'])) {
            $minCount = (int) $filters['members_min'];
            $query->where('number_of_members', '>=', $minCount);
        }

        // فلتر عدد الأفراد - الحد الأقصى (يستخدم حقل number_of_members المخزن)
        if (!empty($filters['members_max'])) {
            $maxCount = (int) $filters['members_max'];
            $query->where('number_of_members', '<=', $maxCount);
        }

        // إضافة عدد التوزيعات
        $query->withCount([
            'familyMembers',
            'distributions as distributions_count'
        ])->with('familyMembers');

        // Pagination - عرض عدد النتائج لكل صفحة (افتراضي 100)
        $perPage = (int) ($filters['per_page'] ?? 100);
        // السماح بقيم محدودة فقط لتحسين الأداء
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 100;
        }
        
        // Paginate without query string (clean URLs)
        $beneficiaries = $query->latest()->paginate($perPage);
        
        // Set custom pagination path without query string
        $beneficiaries->setPath(route('beneficiaries.index'));

        // التحقق من أن الصفحة المطلوبة موجودة، وإلا إعادة التوجيه إلى آخر صفحة
        if ($beneficiaries->lastPage() > 0 && $beneficiaries->currentPage() > $beneficiaries->lastPage()) {
            return redirect()->route('beneficiaries.index', ['page' => $beneficiaries->lastPage()]);
        }

        // تحميل التوزيعات والطرود مع العناصر وحساب إجمالي المساعدات لكل مستفيد
        $beneficiaries->load(['distributions.items', 'batches']);
        foreach ($beneficiaries as $beneficiary) {
            // حساب إجمالي المساعدات من التوزيعات (القديمة)
            $totalAid = 0;
            foreach ($beneficiary->distributions as $distribution) {
                foreach ($distribution->items as $item) {
                    $totalAid += $item->quantity;
                }
            }
            $beneficiary->total_aid_quantity = $totalAid;
            
            // حساب عدد الطرود المخصصة (المستفادة)
            $beneficiary->total_batches_count = $beneficiary->batches->count();
            
            // حساب عدد الطرود المستلمة
            $beneficiary->received_batches_count = $beneficiary->batches->filter(function($batch) {
                return $batch->pivot->received ?? false;
            })->count();
        }

        // التحقق من وجود batch_id في الـ request (للاختيار من صفحة المستفيدين)
        $batchId = $filters['batch_id'] ?? null;
        $batch = null;
        if ($batchId) {
            $batch = \App\Models\Batch::with('beneficiaries')->find($batchId);
        }

        // Get all beneficiaries for Select2 dropdown (limited to 100 for initial load)
        $allBeneficiaries = Beneficiary::select('id', 'name', 'national_id', 'phone')
            ->orderBy('name')
            ->limit(100)
            ->get();
        
        return view('beneficiaries.index', compact('beneficiaries', 'batch', 'allBeneficiaries', 'filters'));
    }

    public function loadMore(Request $request)
    {
        // استخدام نفس منطق index
        $query = Beneficiary::query();

        // البحث المتقدم - في حقول متعددة
        if ($request->filled('beneficiary_id')) {
            $query->where('id', $request->beneficiary_id);
        } elseif ($request->filled('search')) {
            // البحث النصي المحسّن
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('familyMembers', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('national_id', 'like', "%{$search}%");
                  });
            });
        }

        // فلتر الحالة السكنية
        if ($request->filled('residence_status')) {
            $query->where('residence_status', $request->residence_status);
        }

        // فلتر العلاقة - البحث في القيم الجديدة والقديمة
        if ($request->filled('relationship')) {
            $relationshipValues = Beneficiary::getRelationshipValuesForSearch($request->relationship);
            $query->whereIn('relationship', $relationshipValues);
        }

        // فلتر الحالة (موجود/غير موجود)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // فلتر الحوامل
        if ($request->filled('has_pregnant') && $request->has_pregnant) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_pregnant', true);
            });
        }

        // فلتر المرضعات
        if ($request->filled('has_nursing') && $request->has_nursing) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_nursing', true);
            });
        }

        // فلتر الأطفال (أقل من 18 سنة) - يعتمد على تاريخ الميلاد
        if ($request->filled('has_children') && $request->has_children) {
            $eighteenYearsAgo = Carbon::now()->subYears(18);
            $query->whereHas('familyMembers', function($q) use ($eighteenYearsAgo) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>', $eighteenYearsAgo->format('Y-m-d'));
            });
        }

        // فلتر العمر (أكبر من عمر معين)
        if ($request->filled('age_min')) {
            $minDate = Carbon::now()->subYears($request->age_min)->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($minDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '<=', $minDate);
            });
        }

        // فلتر العمر (أصغر من عمر معين)
        if ($request->filled('age_max')) {
            $maxDate = Carbon::now()->subYears($request->age_max)->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($maxDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>=', $maxDate);
            });
        }

        // فلتر عدد الأفراد - الحد الأدنى (يستخدم حقل number_of_members المخزن)
        if ($request->filled('members_min')) {
            $minCount = (int) $request->members_min;
            $query->where('number_of_members', '>=', $minCount);
        }

        // فلتر عدد الأفراد - الحد الأقصى (يستخدم حقل number_of_members المخزن)
        if ($request->filled('members_max')) {
            $maxCount = (int) $request->members_max;
            $query->where('number_of_members', '<=', $maxCount);
        }

        // إضافة عدد التوزيعات
        $query->withCount([
            'familyMembers',
            'distributions as distributions_count'
        ])->with('familyMembers');

        // Pagination - عرض عدد النتائج لكل صفحة (افتراضي 100)
        $perPage = (int) $request->get('per_page', 100);
        if (!in_array($perPage, [50, 100, 200])) {
            $perPage = 100;
        }
        $beneficiaries = $query->latest()->paginate($perPage)->withQueryString();

        // تحميل التوزيعات والطرود مع العناصر وحساب إجمالي المساعدات لكل مستفيد
        $beneficiaries->load(['distributions.items', 'batches']);
        foreach ($beneficiaries as $beneficiary) {
            // حساب إجمالي المساعدات من التوزيعات (القديمة)
            $totalAid = 0;
            foreach ($beneficiary->distributions as $distribution) {
                foreach ($distribution->items as $item) {
                    $totalAid += $item->quantity;
                }
            }
            $beneficiary->total_aid_quantity = $totalAid;
            
            // حساب عدد الطرود المخصصة (المستفادة)
            $beneficiary->total_batches_count = $beneficiary->batches->count();
            
            // حساب عدد الطرود المستلمة
            $beneficiary->received_batches_count = $beneficiary->batches->filter(function($batch) {
                return $batch->pivot->received ?? false;
            })->count();
        }

        // التحقق من وجود batch_id في الـ request
        $batchId = $request->get('batch_id');
        $batch = null;
        if ($batchId) {
            $batch = \App\Models\Batch::with('beneficiaries')->find($batchId);
        }

        $html = view('beneficiaries.partials.rows', compact('beneficiaries', 'batch'))->render();

        return response()->json([
            'html' => $html,
            'hasMore' => $beneficiaries->hasMorePages(),
            'currentPage' => $beneficiaries->currentPage(),
            'total' => $beneficiaries->total()
        ]);
    }

    public function create()
    {
        return view('beneficiaries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'relationship' => 'required|string|max:100',
            'address' => 'nullable|string',
            'national_id' => 'required|string|max:50',
            'residence_status' => 'required|in:resident,displaced',
            'number_of_members' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'family_members' => 'nullable|array',
            'family_members.*.name' => 'required|string|max:255',
            'family_members.*.national_id' => 'nullable|string|max:50',
            'family_members.*.birth_date' => 'nullable|date',
            'family_members.*.relationship' => 'nullable|string|max:100',
            'family_members.*.is_pregnant' => 'boolean',
            'family_members.*.is_nursing' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $beneficiary = Beneficiary::create($validated);

            if (isset($validated['family_members']) && is_array($validated['family_members'])) {
                foreach ($validated['family_members'] as $index => $member) {
                    FamilyMember::create([
                        'beneficiary_id' => $beneficiary->id,
                        'name' => $member['name'],
                        'national_id' => $member['national_id'] ?? null,
                        'birth_date' => $member['birth_date'] ?? null,
                        'relationship' => $member['relationship'] ?? null,
                        'is_pregnant' => !empty($member['is_pregnant']),
                        'is_nursing' => !empty($member['is_nursing']),
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('beneficiaries.index')
                ->with('success', 'تم إضافة المستفيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المستفيد: ' . $e->getMessage());
        }
    }

    public function show(Beneficiary $beneficiary)
    {
        $beneficiary->load('distributions.items.package', 'familyMembers', 'batches');
        return view('beneficiaries.show', compact('beneficiary'));
    }

    public function edit(Beneficiary $beneficiary)
    {
        $beneficiary->load('familyMembers');
        return view('beneficiaries.edit', compact('beneficiary'));
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'relationship' => 'required|string|max:100',
            'address' => 'nullable|string',
            'national_id' => 'required|string|max:50',
            'residence_status' => 'required|in:resident,displaced',
            'number_of_members' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'family_members' => 'nullable|array',
            'family_members.*.name' => 'required|string|max:255',
            'family_members.*.national_id' => 'nullable|string|max:50',
            'family_members.*.birth_date' => 'nullable|date',
            'family_members.*.relationship' => 'nullable|string|max:100',
            'family_members.*.is_pregnant' => 'boolean',
            'family_members.*.is_nursing' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $beneficiary->update($validated);

            // حذف أفراد الأسرة القديمين
            $beneficiary->familyMembers()->delete();

            // إضافة أفراد الأسرة الجدد
            if (isset($validated['family_members']) && is_array($validated['family_members'])) {
                foreach ($validated['family_members'] as $index => $member) {
                    FamilyMember::create([
                        'beneficiary_id' => $beneficiary->id,
                        'name' => $member['name'],
                        'national_id' => $member['national_id'] ?? null,
                        'birth_date' => $member['birth_date'] ?? null,
                        'relationship' => $member['relationship'] ?? null,
                        'is_pregnant' => !empty($member['is_pregnant']),
                        'is_nursing' => !empty($member['is_nursing']),
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('beneficiaries.index')
                ->with('success', 'تم تحديث بيانات المستفيد بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستفيد: ' . $e->getMessage());
        }
    }

    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();

        return redirect()->route('beneficiaries.index')
            ->with('success', 'تم حذف المستفيد بنجاح');
    }

    public function toggleStatus(Beneficiary $beneficiary)
    {
        $beneficiary->update(['is_active' => !$beneficiary->is_active]);

        return redirect()->back()
            ->with('success', $beneficiary->is_active ? 'تم تفعيل المستفيد' : 'تم تعطيل المستفيد');
    }

    /**
     * Update beneficiary profile (for self-service)
     */
    public function updateProfile(Request $request)
    {
        $beneficiaryId = session('beneficiary_id');
        if (!$beneficiaryId) {
            return redirect()->route('beneficiary.verify');
        }

        $beneficiary = Beneficiary::findOrFail($beneficiaryId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string|max:100',
            'residence_status' => 'nullable|in:resident,displaced',
            'number_of_members' => 'nullable|integer|min:1',
            'family_members' => 'nullable|array',
            'family_members.*.name' => 'required|string|max:255',
            'family_members.*.national_id' => 'nullable|string|max:50',
            'family_members.*.birth_date' => 'nullable|date',
            'family_members.*.relationship' => 'nullable|string|max:100',
            'family_members.*.is_pregnant' => 'boolean',
            'family_members.*.is_nursing' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $beneficiary->update($validated);

            // Update family members
            if (isset($validated['family_members']) && is_array($validated['family_members'])) {
                $beneficiary->familyMembers()->delete();
                foreach ($validated['family_members'] as $index => $member) {
                    FamilyMember::create([
                        'beneficiary_id' => $beneficiary->id,
                        'name' => $member['name'],
                        'national_id' => $member['national_id'] ?? null,
                        'birth_date' => $member['birth_date'] ?? null,
                        'relationship' => $member['relationship'] ?? null,
                        'is_pregnant' => !empty($member['is_pregnant']),
                        'is_nursing' => !empty($member['is_nursing']),
                        'order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('beneficiary.profile')
                ->with('success', 'تم تحديث بياناتك بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->get('q', '');
            
            $query = Beneficiary::query();
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('national_id', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }
            
            $beneficiaries = $query->select('id', 'name', 'national_id', 'phone')
                ->orderBy('name')
                ->limit(100)
                ->get();
            
            $results = $beneficiaries->map(function($beneficiary) {
                $text = $beneficiary->name;
                if ($beneficiary->national_id) {
                    $text .= ' - ' . $beneficiary->national_id;
                }
                if ($beneficiary->phone) {
                    $text .= ' - ' . $beneficiary->phone;
                }
                return [
                    'id' => $beneficiary->id,
                    'text' => $text
                ];
            });
            
            return response()->json([
                'results' => $results->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error('Beneficiary search error: ' . $e->getMessage());
            return response()->json([
                'results' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFilteredIds(Request $request)
    {
        try {
            $query = Beneficiary::query();
            
            // تطبيق نفس الفلاتر من index
            if ($request->filled('beneficiary_id')) {
                $query->where('id', $request->beneficiary_id);
            } elseif ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('national_id', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('familyMembers', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                             ->orWhere('national_id', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('residence_status')) {
                $query->where('residence_status', $request->residence_status);
            }

            if ($request->filled('relationship')) {
                $relationshipValues = Beneficiary::getRelationshipValuesForSearch($request->relationship);
                $query->whereIn('relationship', $relationshipValues);
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            if ($request->filled('has_pregnant') && $request->has_pregnant) {
                $query->whereHas('familyMembers', function($q) {
                    $q->where('is_pregnant', true);
                });
            }

            if ($request->filled('has_nursing') && $request->has_nursing) {
                $query->whereHas('familyMembers', function($q) {
                    $q->where('is_nursing', true);
                });
            }

            if ($request->filled('has_children') && $request->has_children) {
                $eighteenYearsAgo = Carbon::now()->subYears(18);
                $query->whereHas('familyMembers', function($q) use ($eighteenYearsAgo) {
                    $q->whereNotNull('birth_date')
                      ->where('birth_date', '>', $eighteenYearsAgo->format('Y-m-d'));
                });
            }

            if ($request->filled('age_min')) {
                $minDate = Carbon::now()->subYears($request->age_min)->format('Y-m-d');
                $query->whereHas('familyMembers', function($q) use ($minDate) {
                    $q->whereNotNull('birth_date')
                      ->where('birth_date', '<=', $minDate);
                });
            }

            if ($request->filled('age_max')) {
                $maxDate = Carbon::now()->subYears($request->age_max)->format('Y-m-d');
                $query->whereHas('familyMembers', function($q) use ($maxDate) {
                    $q->whereNotNull('birth_date')
                      ->where('birth_date', '>=', $maxDate);
                });
            }

            if ($request->filled('members_min')) {
                $minCount = (int) $request->members_min;
                $query->where('number_of_members', '>=', $minCount);
            }

            if ($request->filled('members_max')) {
                $maxCount = (int) $request->members_max;
                $query->where('number_of_members', '<=', $maxCount);
            }

            // إذا كان هناك batch_id، استثناء المستفيدين الموجودين فيه
            if ($request->filled('batch_id')) {
                $batchId = $request->batch_id;
                $batch = \App\Models\Batch::find($batchId);
                if ($batch) {
                    $existingBeneficiaryIds = $batch->beneficiaries()->pluck('beneficiaries.id')->toArray();
                    if (!empty($existingBeneficiaryIds)) {
                        $query->whereNotIn('id', $existingBeneficiaryIds);
                    }
                }
            }

            // جلب جميع IDs فقط
            $ids = $query->pluck('id')->toArray();
            
            return response()->json([
                'success' => true,
                'ids' => $ids,
                'count' => count($ids)
            ]);
        } catch (\Exception $e) {
            \Log::error('Get filtered IDs error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        // Get filters from session (same as index)
        $filters = $this->getFiltersFromSession();
        
        $query = Beneficiary::query();

        // تطبيق نفس الفلاتر من index
        if (!empty($filters['beneficiary_id'])) {
            $query->where('id', $filters['beneficiary_id']);
        } elseif (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('familyMembers', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('national_id', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['residence_status'])) {
            $query->where('residence_status', $filters['residence_status']);
        }

        if (!empty($filters['relationship'])) {
            $relationshipValues = Beneficiary::getRelationshipValuesForSearch($filters['relationship']);
            $query->whereIn('relationship', $relationshipValues);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['has_pregnant'])) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_pregnant', true);
            });
        }

        if (!empty($filters['has_nursing'])) {
            $query->whereHas('familyMembers', function($q) {
                $q->where('is_nursing', true);
            });
        }

        if (!empty($filters['has_children'])) {
            $eighteenYearsAgo = Carbon::now()->subYears(18);
            $query->whereHas('familyMembers', function($q) use ($eighteenYearsAgo) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>', $eighteenYearsAgo->format('Y-m-d'));
            });
        }

        if (!empty($filters['age_min'])) {
            $minDate = Carbon::now()->subYears($filters['age_min'])->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($minDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '<=', $minDate);
            });
        }

        if (!empty($filters['age_max'])) {
            $maxDate = Carbon::now()->subYears($filters['age_max'])->format('Y-m-d');
            $query->whereHas('familyMembers', function($q) use ($maxDate) {
                $q->whereNotNull('birth_date')
                  ->where('birth_date', '>=', $maxDate);
            });
        }

        if (!empty($filters['members_min'])) {
            $minCount = (int) $filters['members_min'];
            $query->where('number_of_members', '>=', $minCount);
        }

        if (!empty($filters['members_max'])) {
            $maxCount = (int) $filters['members_max'];
            $query->where('number_of_members', '<=', $maxCount);
        }

        $beneficiaries = $query->withCount([
            'familyMembers',
            'distributions as distributions_count'
        ])->with(['familyMembers', 'distributions.items'])->latest()->get();

        try {
            // استخدام PhpSpreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('المستفيدون');
            
            // العناوين
            $headers = [
                'الاسم',
                'رقم الهوية',
                'الهاتف',
                'عدد الأفراد',
                'إجمالي المساعدات',
                'العلاقة',
                'الحالة السكنية',
                'الحالة',
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
                $residenceStatus = $beneficiary->residence_status === 'displaced' ? 'نازح' : 'مقيم';
                $status = $beneficiary->is_active ? 'نشط' : 'معطل';
                
                // حساب إجمالي المساعدات
                $totalAid = 0;
                foreach ($beneficiary->distributions ?? [] as $distribution) {
                    foreach ($distribution->items ?? [] as $item) {
                        $totalAid += $item->quantity ?? 0;
                    }
                }
                
                $data = [
                    $beneficiary->name,
                    $beneficiary->national_id ?? '-',
                    $beneficiary->phone ?? '-',
                    ($beneficiary->familyMembers ? $beneficiary->familyMembers->count() : 0) + 1,
                    $totalAid,
                    $beneficiary->relationship ?? '-',
                    $residenceStatus,
                    $status,
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
            
            // إنشاء Writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // حفظ في ملف مؤقت
            $filename = 'beneficiaries-' . date('Y-m-d') . '.xlsx';
            $tempFile = sys_get_temp_dir() . '/' . uniqid() . '_' . $filename;
            $writer->save($tempFile);
            
            // إرجاع الملف كـ download
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return redirect()->route('beneficiaries.index')
                ->with('error', 'حدث خطأ أثناء تصدير Excel: ' . $e->getMessage());
        }
    }
}
