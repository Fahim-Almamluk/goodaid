@extends('layouts.app')

@section('title', 'تفاصيل المستفيد: ' . $beneficiary->name)

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">{{ $beneficiary->name }}</h1>
            <p class="text-gray-600 mt-2 text-lg">تفاصيل المستفيد</p>
        </div>
        <div class="flex items-center gap-4">
            @hasPermission('beneficiaries.edit')
            <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 text-sm font-semibold">
                تعديل
            </a>
            @endhasPermission
            <a href="{{ route('beneficiaries.index') }}" class="px-5 py-2.5 bg-red-500 text-white rounded-xl hover:bg-red-600 hover:shadow-lg transition-all duration-200 text-sm font-semibold flex items-center gap-2">
                رجوع
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Basic Info -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-10">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">البيانات الأساسية</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <label class="text-base font-semibold text-gray-600 mb-2 block">الاسم الكامل</label>
                <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->name }}</p>
            </div>
            <div>
                <label class="text-base font-semibold text-gray-600 mb-2 block">رقم الهوية</label>
                <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->national_id ?? '-' }}</p>
            </div>
                    <div>
                        <label class="text-base font-semibold text-gray-600 mb-2 block">رقم الهاتف</label>
                <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-base font-semibold text-gray-600 mb-2 block">العلاقة</label>
                <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->formatted_relationship ?? '-' }}</p>
                </div>
            <div>
                <label class="text-base font-semibold text-gray-600 mb-2 block">الحالة السكنية</label>
                <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->residence_status === 'displaced' ? 'نازح' : 'مقيم' }}</p>
            </div>
            <div>
                <label class="text-base font-semibold text-gray-600 mb-2 block">عدد الأفراد</label>
                <p class="text-xl text-gray-900 font-medium">{{ ($beneficiary->familyMembers ? $beneficiary->familyMembers->count() : 0) + 1 }}</p>
            </div>
            @if($beneficiary->address)
                <div>
                    <label class="text-base font-semibold text-gray-600 mb-2 block">العنوان</label>
                    <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->address }}</p>
                </div>
            @endif
            <div>
                <label class="text-base font-semibold text-gray-600 mb-2 block">الحالة</label>
                <span class="inline-flex items-center px-4 py-2 rounded-full text-base font-semibold {{ $beneficiary->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $beneficiary->is_active ? 'موجود' : 'غير موجود' }}
                </span>
            </div>
            @if($beneficiary->notes)
                <div class="md:col-span-2">
                    <label class="text-base font-semibold text-gray-600 mb-2 block">ملاحظات</label>
                    <p class="text-xl text-gray-900 font-medium">{{ $beneficiary->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Family Members -->
    @if($beneficiary->familyMembers && $beneficiary->familyMembers->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">أفراد الأسرة ({{ $beneficiary->familyMembers->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-600">
                    <tr>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">الاسم</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">رقم الهوية</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">تاريخ الميلاد</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">العلاقة</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($beneficiary->familyMembers as $member)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-5 text-base font-medium text-gray-900">{{ $member->name }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">{{ $member->national_id ?? '-' }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">{{ $member->birth_date ? $member->birth_date->format('Y-m-d') : '-' }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">{{ $member->relationship ?? '-' }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($member->is_pregnant)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-pink-100 text-pink-800">حامل</span>
                                @endif
                                @if($member->is_nursing)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">مرضعة</span>
                                @endif
                                @if(!$member->is_pregnant && !$member->is_nursing)
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Batches (الطرود المستفادة) -->
    @if($beneficiary->batches && $beneficiary->batches->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">سجل الطرود المخصصة ({{ $beneficiary->batches->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-600">
                    <tr>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">اسم الطرد</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">الحالة</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">حالة الاستلام</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">المدخل</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">تاريخ ووقت التسليم</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($beneficiary->batches as $batch)
                        @php
                            $received = $batch->pivot->received ?? false;
                            $receivedAt = $batch->pivot->received_at ?? null;
                            $approvedBy = $batch->pivot->approved_by ?? null;
                            $deliveredBy = $approvedBy ? \App\Models\User::find($approvedBy) : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-5 text-base font-medium text-gray-900">{{ $batch->name }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($batch->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">نشط</span>
                                @elseif($batch->status === 'completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">مكتمل</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">{{ $batch->status }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($received)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800">تم الاستلام</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">لم يتم الاستلام</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                {{ $deliveredBy ? $deliveredBy->name : '-' }}
                            </td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($receivedAt)
                                    {{ \Carbon\Carbon::parse($receivedAt)->format('Y-m-d H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($batch->status === 'active')
                                    <a href="{{ route('batches.distribution', $batch) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all text-sm font-semibold">
                                        عرض
                                    </a>
                                @else
                                    <a href="{{ route('batches.manage', $batch) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all text-sm font-semibold">
                                        عرض
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Distributions -->
    @if($beneficiary->distributions && $beneficiary->distributions->count() > 0)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="px-8 py-5 bg-gray-50 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">سجل التوزيعات ({{ $beneficiary->distributions->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-emerald-500 to-teal-600">
                    <tr>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">تاريخ التوزيع</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">الطرود</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">الموزع</th>
                        <th class="px-8 py-4 text-right text-base font-semibold text-white">ملاحظات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($beneficiary->distributions as $distribution)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-8 py-5 text-base text-gray-900">{{ $distribution->distributed_at ? $distribution->distributed_at->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">
                                @if($distribution->items && $distribution->items->count() > 0)
                                    <ul class="list-disc list-inside">
                                    @foreach($distribution->items as $item)
                                            <li>{{ $item->package->name ?? '-' }} ({{ $item->quantity }} {{ $item->package->unit ?? '' }})</li>
                                    @endforeach
                                    </ul>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-8 py-5 text-base text-gray-900">{{ $distribution->distributor->name ?? '-' }}</td>
                            <td class="px-8 py-5 text-base text-gray-900">{{ $distribution->notes ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
