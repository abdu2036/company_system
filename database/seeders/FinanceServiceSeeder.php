<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceService;

class FinanceServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'تصديق عقد تأسيس'],
            ['name' => 'سجل تجاري'],
            ['name' => 'غرفة تجارية'],
            ['name' => 'ترخيص'],
            ['name' => 'رمز احصائي'],
            ['name' => 'عقد اجار + موقع + رقم ضريبي'],
            ['name' => 'شهادة سلبية'],
            ['name' => 'تصديق عقود + نظام + محضر اجتماع'],
            ['name' => 'تضامن'],
            ['name' => 'اتعاب موظف'],
            ['name' => 'سجل مستوردين'],
            ['name' => 'ختم + شعار'],
            ['name' => 'سجل صناعي'],
            ['name' => 'رسالة مصرف'],
            ['name' => 'تفويض'],
            ['name' => 'الضمان'],
        ];

        foreach ($services as $service) {
            FinanceService::create($service);
        }
    }
}