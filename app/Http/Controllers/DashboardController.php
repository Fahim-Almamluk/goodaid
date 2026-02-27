<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Distribution;
use App\Models\Batch;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_beneficiaries' => Beneficiary::count(),
            'active_beneficiaries' => Beneficiary::active()->count(),
            'inactive_beneficiaries' => Beneficiary::where('is_active', false)->count(),
            
            // إحصائيات الطرود
            'total_batches' => Batch::count(),
            'draft_batches' => Batch::where('status', 'draft')->count(),
            'active_batches' => Batch::where('status', 'active')->count(),
            
            // إحصائيات التوزيعات القديمة
            'total_distributions' => Distribution::count(),
            'distributions_today' => Distribution::whereDate('distributed_at', today())->count(),
            'distributions_this_month' => Distribution::whereMonth('distributed_at', now()->month)
                ->whereYear('distributed_at', now()->year)
                ->count(),
            
            // إحصائيات المستفيدين من الطرود
            'total_batch_beneficiaries' => \DB::table('batch_recipients')->count(),
            'received_today' => \DB::table('batch_recipients')
                ->whereDate('received_at', today())
                ->where('received', true)
                ->count(),
            'received_this_month' => \DB::table('batch_recipients')
                ->whereMonth('received_at', now()->month)
                ->whereYear('received_at', now()->year)
                ->where('received', true)
                ->count(),
        ];

        // آخر التوزيعات القديمة
        $recentDistributions = Distribution::with(['beneficiary', 'distributor'])
            ->latest('distributed_at')
            ->take(5)
            ->get();

        // آخر الطرود
        $recentBatches = Batch::withCount('beneficiaries')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentDistributions', 'recentBatches'));
    }
}
