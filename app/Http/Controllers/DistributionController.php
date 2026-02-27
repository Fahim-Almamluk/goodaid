<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    public function index()
    {
        $distributions = Distribution::with(['beneficiary', 'distributor'])
            ->latest('distributed_at')
            ->paginate(15);
        return view('distributions.index', compact('distributions'));
    }

    public function create()
    {
        $beneficiaries = Beneficiary::active()->get();
        return view('distributions.create', compact('beneficiaries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'distributed_at' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Use authenticated user
            $userId = auth()->id();
            
            $distribution = Distribution::create([
                'beneficiary_id' => $validated['beneficiary_id'],
                'distributed_by' => $userId,
                'distributed_at' => $validated['distributed_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $distribution->items()->create([
                    'quantity' => $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('distributions.index')
                ->with('success', 'تم تسجيل التوزيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(Distribution $distribution)
    {
        $distribution->load(['beneficiary', 'distributor', 'items']);
        return view('distributions.show', compact('distribution'));
    }

    public function destroy(Distribution $distribution)
    {
        DB::beginTransaction();
        try {
            $distribution->delete();

            DB::commit();

            return redirect()->route('distributions.index')
                ->with('success', 'تم حذف التوزيع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء الحذف');
        }
    }
}
