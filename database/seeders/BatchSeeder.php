<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Batch;
use App\Models\Beneficiary;
use Carbon\Carbon;

class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['food', 'health', 'clothes'];
        $statuses = ['draft', 'active'];
        
        // جلب جميع المستفيدين النشطين
        $beneficiaries = Beneficiary::where('is_active', true)->pluck('id')->toArray();
        
        if (empty($beneficiaries)) {
            $this->command->warn('لا يوجد مستفيدين نشطين في قاعدة البيانات!');
            return;
        }

        $this->command->info('جاري إنشاء 205 طرد...');

        for ($i = 1; $i <= 205; $i++) {
            $type = $types[array_rand($types)];
            $status = $statuses[array_rand($statuses)];
            
            // إنشاء الطرد
            $batch = Batch::create([
                'name' => 'طرد رقم ' . $i,
                'type' => $type,
                'batch_date' => Carbon::now()->subDays(rand(0, 60)),
                'quantity' => rand(50, 500),
                'status' => $status,
                'notes' => 'طرد تجريبي للفحص والاختبار',
            ]);

            // إضافة مستفيدين عشوائيين للطرد
            $numberOfBeneficiaries = rand(10, 50);
            $selectedBeneficiaries = array_rand(array_flip($beneficiaries), min($numberOfBeneficiaries, count($beneficiaries)));
            
            if (!is_array($selectedBeneficiaries)) {
                $selectedBeneficiaries = [$selectedBeneficiaries];
            }

            foreach ($selectedBeneficiaries as $beneficiaryId) {
                $received = (bool)rand(0, 1);
                
                $batch->beneficiaries()->attach($beneficiaryId, [
                    'received' => $received,
                    'received_at' => $received ? Carbon::now()->subDays(rand(0, 30)) : null,
                    'approved_by' => $received ? 1 : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($i % 20 == 0) {
                $this->command->info("تم إنشاء {$i} طرد...");
            }
        }

        $this->command->info('تم إنشاء 205 طرد بنجاح! ✅');
    }
}
