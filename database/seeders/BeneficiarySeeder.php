<?php

namespace Database\Seeders;

use App\Models\Beneficiary;
use App\Models\FamilyMember;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BeneficiarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'أحمد محمد حسن',
            'فاطمة علي إبراهيم',
            'محمد خالد سعيد',
            'سارة عبدالله محمود',
            'علي يوسف أحمد',
            'مريم حسن علي',
            'خالد محمود عثمان',
            'نورا عبدالرحمن سالم',
            'يوسف إبراهيم خالد',
            'ليلى محمد عبدالله',
            'محمود حسن أحمد',
            'رحمة يوسف علي',
            'سعد عبدالله محمد',
            'هند خالد إبراهيم',
            'عبدالرحمن محمود سعيد',
            'زينب علي حسن',
            'طارق محمد خالد',
            'فادية أحمد يوسف',
            'عمر عبدالله محمود',
            'سمر إبراهيم علي',
            'باسم حسن محمد',
            'نادية خالد أحمد',
            'وليد سعيد محمود',
            'رانيا علي عبدالله',
            'حسام يوسف إبراهيم',
            'هالة محمد حسن',
            'صلاح عبدالرحمن خالد',
            'لمياء أحمد علي',
            'مازن محمود سعيد',
            'سهام يوسف محمد',
            'نادر خالد عبدالله',
            'أمل إبراهيم حسن',
            'عماد محمود علي',
            'شيرين أحمد خالد',
            'وسام محمد يوسف',
            'رنا سعيد عبدالله',
            'باسل علي محمود',
            'داليا إبراهيم حسن',
            'حسني خالد أحمد',
            'جمانة محمد علي',
            'رامي عبدالله يوسف',
            'نهى محمود خالد',
            'زياد سعيد إبراهيم',
            'لينا أحمد محمد',
            'مروان علي حسن',
            'تمارا خالد محمود',
            'رياض يوسف عبدالله',
            'رانيا سعيد علي',
            'ياسر إبراهيم أحمد',
            'سوسن محمد خالد',
            'معاذ محمود حسن',
            'أسماء يوسف علي',
            'عبدالله سعيد إبراهيم',
            'تغريد أحمد محمود',
            'شادي خالد محمد',
            'مها علي يوسف',
            'بشير عبدالله سعيد',
            'ريما محمود إبراهيم',
            'وائل حسن خالد',
        ];

        $addresses = [
            'حي الشهداء، شارع النصر',
            'حي السلام، شارع السلام',
            'حي الوحدة، شارع الوحدة',
            'حي الأمل، شارع الأمل',
            'حي القدس، شارع القدس',
            'حي الفلاح، شارع الفلاح',
            'حي النور، شارع النور',
            'حي الخير، شارع الخير',
            'حي الجنان، شارع الجنان',
            'حي الزهور، شارع الزهور',
            'حي الأندلس، شارع الأندلس',
            'حي الرافدة، شارع الرافدة',
        ];

        $notes = [
            'مستفيد نشط',
            'يحتاج مساعدة مستمرة',
            'أسرة كبيرة',
            'حالة خاصة',
            'يحتاج متابعة',
            null,
            'أسرة صغيرة',
            'حالة جيدة',
            null,
            'يحتاج دعم غذائي',
        ];

        $relationships = ['زوج/زوجة', 'أرمل/أرملة'];
        $residenceStatuses = ['resident', 'displaced'];
        
        $beneficiaries = [];
        
        for ($i = 0; $i < 60; $i++) {
            $nationalId = '1' . str_pad($i + 1, 10, '0', STR_PAD_LEFT); // رقم هوية وطني فريد
            $phone = '09' . rand(10000000, 99999999); // رقم هاتف سوري
            
            $beneficiary = Beneficiary::create([
                'name' => $names[$i % count($names)] . ($i >= count($names) ? ' ' . ($i + 1) : ''),
                'national_id' => $nationalId,
                'phone' => $phone,
                'relationship' => $relationships[array_rand($relationships)],
                'residence_status' => $residenceStatuses[array_rand($residenceStatuses)],
                'address' => $addresses[array_rand($addresses)] . ' ' . rand(1, 100),
                'number_of_members' => rand(1, 8),
                'notes' => $notes[array_rand($notes)],
                'is_active' => rand(0, 10) < 8, // 80% نشط
            ]);

            // إضافة أفراد العائلة (بعض المستفيدين)
            if (rand(0, 10) > 4) { // حوالي 60% لديهم أفراد عائلة
                $numMembers = rand(1, min(5, $beneficiary->number_of_members));
                
                $familyRelationships = ['ابن', 'ابنة', 'زوج', 'زوجة'];
                $order = 0;
                
                for ($j = 0; $j < $numMembers; $j++) {
                    $birthYear = rand(1950, 2020);
                    $birthMonth = rand(1, 12);
                    $birthDay = rand(1, 28);
                    
                    $isPregnant = false;
                    $isNursing = false;
                    
                    // إذا كان زوجة، قد تكون حامل أو مرضعة
                    if ($familyRelationships[array_rand($familyRelationships)] === 'زوجة' && rand(0, 10) > 7) {
                        $isPregnant = rand(0, 1) === 1;
                        $isNursing = !$isPregnant && rand(0, 1) === 1;
                    }
                    
                    FamilyMember::create([
                        'beneficiary_id' => $beneficiary->id,
                        'name' => $names[array_rand($names)] . ' ' . ($j + 1),
                        'national_id' => '2' . str_pad($i * 10 + $j, 11, '0', STR_PAD_LEFT),
                        'birth_date' => Carbon::create($birthYear, $birthMonth, $birthDay),
                        'relationship' => $familyRelationships[array_rand($familyRelationships)],
                        'is_pregnant' => $isPregnant,
                        'is_nursing' => $isNursing,
                        'order' => $order++,
                    ]);
                }
            }
        }
    }
}















