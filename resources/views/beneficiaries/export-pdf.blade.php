<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المستفيدين</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            font-size: 12px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #10b981;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #065f46;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            color: #6b7280;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        th {
            background: linear-gradient(to right, #10b981, #14b8a6);
            color: white;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 11px;
            border: 1px solid #059669;
        }
        
        td {
            padding: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
        }
        
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-yellow { background-color: #fef3c7; color: #92400e; }
        .badge-orange { background-color: #fed7aa; color: #9a3412; }
        .badge-indigo { background-color: #e0e7ff; color: #3730a3; }
        
        .footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير المستفيدين</h1>
        <p>تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>الاسم</th>
                <th>رقم الهوية</th>
                <th>الهاتف</th>
                <th>عدد الأفراد</th>
                <th>إجمالي المساعدات</th>
                <th>العلاقة</th>
                <th>الحالة السكنية</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @foreach($beneficiaries as $beneficiary)
                @php
                    $children = $beneficiary->familyMembers->filter(function($member) {
                        return $member->birth_date && $member->birth_date->age < 18;
                    });
                @endphp
                <tr>
                    <td>{{ $beneficiary->name }}</td>
                    <td>{{ $beneficiary->national_id ?? '-' }}</td>
                    <td>{{ $beneficiary->phone ?? '-' }}</td>
                    <td>{{ $beneficiary->familyMembers->count() + 1 }}</td>
                    <td>{{ $beneficiary->total_aid_quantity ?? 0 }}</td>
                    <td>{{ $beneficiary->formatted_relationship ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $beneficiary->residence_status === 'displaced' ? 'badge-orange' : 'badge-blue' }}">
                            {{ $beneficiary->residence_status === 'displaced' ? 'نازح' : 'مقيم' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $beneficiary->is_active ? 'badge-green' : 'badge-orange' }}">
                            {{ $beneficiary->is_active ? 'موجود' : 'غير موجود' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>إجمالي المستفيدين: {{ $beneficiaries->count() }}</p>
        <p>GoodAid - نظام إدارة المساعدات</p>
    </div>
</body>
</html>

