<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Prescription;
use App\Models\Tooth;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // 1. USERS (6 مستخدمين)
        // =====================

        $admin = User::firstOrCreate(
            ['email' => 'admin@dental.com'],
            [
                'name'      => 'أحمد السيد — المدير',
                'password'  => Hash::make('password'),
                'role'      => 'admin',
                'is_active' => true,
                'phone'     => '01001000000',
            ]
        );

        $rec1 = User::firstOrCreate(
            ['email' => 'sara@dental.com'],
            [
                'name'      => 'سارة محمد — استقبال',
                'password'  => Hash::make('password'),
                'role'      => 'receptionist',
                'is_active' => true,
                'phone'     => '01101000001',
            ]
        );

        $rec2 = User::firstOrCreate(
            ['email' => 'nada@dental.com'],
            [
                'name'      => 'ندى علي — استقبال',
                'password'  => Hash::make('password'),
                'role'      => 'receptionist',
                'is_active' => true,
                'phone'     => '01201000002',
            ]
        );

        $dr1 = User::firstOrCreate(
            ['email' => 'dr.omar@dental.com'],
            [
                'name'      => 'د. عمر حسن',
                'password'  => Hash::make('password'),
                'role'      => 'doctor',
                'is_active' => true,
                'phone'     => '01001100001',
            ]
        );

        $dr2 = User::firstOrCreate(
            ['email' => 'dr.maya@dental.com'],
            [
                'name'      => 'د. مايا إبراهيم',
                'password'  => Hash::make('password'),
                'role'      => 'doctor',
                'is_active' => true,
                'phone'     => '01001100002',
            ]
        );

        $dr3 = User::firstOrCreate(
            ['email' => 'dr.tarek@dental.com'],
            [
                'name'      => 'د. طارق رضا',
                'password'  => Hash::make('password'),
                'role'      => 'doctor',
                'is_active' => true,
                'phone'     => '01001100003',
            ]
        );

        $dr4 = User::firstOrCreate(
            ['email' => 'dr.hana@dental.com'],
            [
                'name'      => 'د. هناء سامي',
                'password'  => Hash::make('password'),
                'role'      => 'doctor',
                'is_active' => true,
                'phone'     => '01001100004',
            ]
        );

        // =====================
        // 2. DOCTOR PROFILES
        // =====================

        Doctor::firstOrCreate(['user_id' => $dr1->id], [
            'specialty'       => 'تقويم الأسنان',
            'commission_rate' => 30.00,
            'notes'           => 'متخصص في التقويم الشفاف والمعدني',
        ]);

        Doctor::firstOrCreate(['user_id' => $dr2->id], [
            'specialty'       => 'علاج الأعصاب',
            'commission_rate' => 35.00,
            'notes'           => 'خبرة 10 سنوات في علاج قنوات الجذر',
        ]);

        Doctor::firstOrCreate(['user_id' => $dr3->id], [
            'specialty'       => 'زراعة الأسنان',
            'commission_rate' => 40.00,
            'notes'           => 'متخصص في الزراعة الفورية',
        ]);

        Doctor::firstOrCreate(['user_id' => $dr4->id], [
            'specialty'       => 'طب أسنان الأطفال',
            'commission_rate' => 25.00,
            'notes'           => 'متخصصة في أسنان الأطفال والتخدير',
        ]);

        // =====================
        // 3. PATIENTS (20 مريض)
        // =====================

        $patientsData = [
            // Dr. Omar (5 مرضى)
            ['name'=>'محمود إبراهيم خليل',    'phone'=>'01551000001','gender'=>'male',  'dob'=>'1985-03-15','doctor_id'=>$dr1->id,'national_id'=>'28503150101001'],
            ['name'=>'رنا سعيد عبدالله',       'phone'=>'01551000002','gender'=>'female','dob'=>'1992-07-22','doctor_id'=>$dr1->id,'national_id'=>'29207220101002'],
            ['name'=>'كريم طاهر منصور',        'phone'=>'01551000003','gender'=>'male',  'dob'=>'1978-11-05','doctor_id'=>$dr1->id,'national_id'=>'27811050101003'],
            ['name'=>'نيرمين حسام الدين',      'phone'=>'01551000004','gender'=>'female','dob'=>'2000-01-30','doctor_id'=>$dr1->id,'national_id'=>'30001300101004'],
            ['name'=>'يوسف عصام الشافعي',      'phone'=>'01551000005','gender'=>'male',  'dob'=>'1990-09-12','doctor_id'=>$dr1->id,'national_id'=>'29009120101005'],

            // Dr. Maya (5 مرضى)
            ['name'=>'فاطمة حسن الزيات',       'phone'=>'01661000001','gender'=>'female','dob'=>'1988-04-18','doctor_id'=>$dr2->id,'national_id'=>'28804180202001'],
            ['name'=>'مصطفى علاء الدين جمال',  'phone'=>'01661000002','gender'=>'male',  'dob'=>'1975-12-03','doctor_id'=>$dr2->id,'national_id'=>'27512030202002'],
            ['name'=>'دينا وليد الرفاعي',       'phone'=>'01661000003','gender'=>'female','dob'=>'1995-06-27','doctor_id'=>$dr2->id,'national_id'=>'29506270202003'],
            ['name'=>'عبدالرحمن فاروق النجار',  'phone'=>'01661000004','gender'=>'male',  'dob'=>'1982-08-14','doctor_id'=>$dr2->id,'national_id'=>'28208140202004'],
            ['name'=>'سلمى أشرف الطيب',         'phone'=>'01661000005','gender'=>'female','dob'=>'1998-02-09','doctor_id'=>$dr2->id,'national_id'=>'29802090202005'],

            // Dr. Tarek (5 مرضى)
            ['name'=>'أحمد رامي الحسيني',       'phone'=>'01771000001','gender'=>'male',  'dob'=>'1970-05-20','doctor_id'=>$dr3->id,'national_id'=>'27005200303001'],
            ['name'=>'إيمان صبري الغزالي',      'phone'=>'01771000002','gender'=>'female','dob'=>'1983-10-11','doctor_id'=>$dr3->id,'national_id'=>'28310110303002'],
            ['name'=>'عمر ماجد البلتاجي',       'phone'=>'01771000003','gender'=>'male',  'dob'=>'1991-03-28','doctor_id'=>$dr3->id,'national_id'=>'29103280303003'],
            ['name'=>'نهى سامح قاسم',           'phone'=>'01771000004','gender'=>'female','dob'=>'1987-07-16','doctor_id'=>$dr3->id,'national_id'=>'28707160303004'],
            ['name'=>'زياد حاتم المنشاوي',      'phone'=>'01771000005','gender'=>'male',  'dob'=>'2001-11-02','doctor_id'=>$dr3->id,'national_id'=>'30111020303005'],

            // Dr. Hana (5 مرضى)
            ['name'=>'لين مأمون عبدالعزيز',    'phone'=>'01881000001','gender'=>'female','dob'=>'2015-04-07','doctor_id'=>$dr4->id,'national_id'=>null],
            ['name'=>'آدم يحيى الشرقاوي',       'phone'=>'01881000002','gender'=>'male',  'dob'=>'2013-08-19','doctor_id'=>$dr4->id,'national_id'=>null],
            ['name'=>'مريم جمال عوض',           'phone'=>'01881000003','gender'=>'female','dob'=>'2017-01-25','doctor_id'=>$dr4->id,'national_id'=>null],
            ['name'=>'عمر أحمد البسيوني',       'phone'=>'01881000004','gender'=>'male',  'dob'=>'2010-06-13','doctor_id'=>$dr4->id,'national_id'=>null],
            ['name'=>'نور الدين صالح عطية',     'phone'=>'01881000005','gender'=>'male',  'dob'=>'2012-09-30','doctor_id'=>$dr4->id,'national_id'=>null],
        ];

        $patients = [];
        foreach ($patientsData as $pd) {
            $existing = Patient::where('phone', $pd['phone'])->first();
            if (!$existing) {
                $patient = Patient::create([
                    'doctor_id'    => $pd['doctor_id'],
                    'name'         => $pd['name'],
                    'phone'        => $pd['phone'],
                    'gender'       => $pd['gender'],
                    'date_of_birth'=> $pd['dob'],
                    'national_id'  => $pd['national_id'],
                    'address'      => 'القاهرة، مصر',
                ]);

                $patient->medicalHistory()->create([
                    'allergy_anesthesia'  => false,
                    'allergy_penicillin'  => false,
                    'has_diabetes'        => in_array($pd['name'], ['محمود إبراهيم خليل','أحمد رامي الحسيني']),
                    'has_heart_disease'   => false,
                    'has_bleeding_disorder'=> false,
                    'is_pregnant'         => false,
                ]);

                $teeth = [];
                for ($i = 1; $i <= 32; $i++) {
                    $teeth[] = [
                        'patient_id'  => $patient->id,
                        'tooth_number'=> $i,
                        'tooth_type'  => 'adult',
                        'status'      => 'healthy',
                    ];
                }
                Tooth::insert($teeth);

                $patients[] = $patient;
            } else {
                $patients[] = $existing;
            }
        }

        // =====================
        // 4. APPOINTMENTS (30 موعد)
        // =====================

        $appointmentTitles = [
            'كشف وفحص عام',
            'تقويم أسنان — متابعة',
            'علاج عصب',
            'تنظيف أسنان',
            'حشو سن',
            'خلع سن',
            'تركيب تاج',
            'استشارة زراعة',
            'متابعة بعد الزراعة',
            'فحص دوري',
        ];

        $statuses = ['completed','completed','completed','confirmed','scheduled','scheduled','cancelled','no_show'];

        $doctorPatientMap = [
            $dr1->id => array_slice($patients, 0, 5),
            $dr2->id => array_slice($patients, 5, 5),
            $dr3->id => array_slice($patients, 10, 5),
            $dr4->id => array_slice($patients, 15, 5),
        ];

        $apptCount = 0;
        $now = Carbon::now();

        foreach ($doctorPatientMap as $doctorId => $drPatients) {
            // كل دكتور عنده ~7-8 مواعيد
            for ($i = 0; $i < 8; $i++) {
                $patient = $drPatients[$i % count($drPatients)];
                $daysOffset = rand(-30, 14);
                $hour = rand(9, 17);
                $minute = [0, 15, 30, 45][rand(0, 3)];
                $startsAt = $now->copy()->addDays($daysOffset)->setTime($hour, $minute);
                $endsAt   = $startsAt->copy()->addMinutes(30);

                $status = $daysOffset < 0
                    ? $statuses[rand(0, 4)]
                    : ($daysOffset === 0 ? 'confirmed' : 'scheduled');

                if (!Appointment::where('patient_id', $patient->id)
                    ->where('starts_at', $startsAt)->exists()) {

                    Appointment::create([
                        'patient_id' => $patient->id,
                        'user_id'    => $doctorId,
                        'doctor_id'  => $doctorId,
                        'starts_at'  => $startsAt,
                        'ends_at'    => $endsAt,
                        'title'      => $appointmentTitles[rand(0, count($appointmentTitles)-1)],
                        'status'     => $status,
                    ]);
                    $apptCount++;
                }

                if ($apptCount >= 30) break 2;
            }
        }

        // =====================
        // 5. INVOICES + PAYMENTS
        // =====================

        $services = [
            ['حشو ضوئي مركب',     800,  1],
            ['تنظيف جير وتلميع',  500,  1],
            ['علاج عصب — أمامي', 2500,  1],
            ['علاج عصب — خلفي',  3500,  1],
            ['تركيب تاج زيركون', 5000,  1],
            ['خلع سن عادي',       400,  1],
            ['خلع سن جراحي',     1200,  1],
            ['زراعة سن',         9000,  1],
            ['تقويم معدني',       6000,  1],
            ['تبييض أسنان',      2000,  1],
            ['طباعة أشعة',        150,  2],
            ['كشف وفحص',          200,  1],
        ];

        $invoiceNum = 1000;
        foreach ($patients as $idx => $patient) {
            // كل مريض عنده فاتورة واحدة أو اتنين
            $numInvoices = ($idx % 3 === 0) ? 2 : 1;
            for ($inv = 0; $inv < $numInvoices; $inv++) {
                $invoiceNum++;
                $invoiceDate = $now->copy()->subDays(rand(1, 60));
                $service1 = $services[rand(0, count($services)-1)];
                $service2 = $services[rand(0, count($services)-1)];

                $total = ($service1[1] * $service1[2]) + ($service2[1] * $service2[2]);

                if (!Invoice::where('invoice_number', 'INV-' . $invoiceNum)->exists()) {
                    $invoice = Invoice::create([
                        'patient_id'     => $patient->id,
                        'user_id'        => $patient->doctor_id ?? $admin->id,
                        'invoice_number' => 'INV-' . $invoiceNum,
                        'invoice_date'   => $invoiceDate->toDateString(),
                        'total_amount'   => $total,
                        'paid_amount'    => 0,
                        'notes'          => null,
                    ]);

                    $invoice->items()->createMany([
                        [
                            'description' => $service1[0],
                            'quantity'    => $service1[2],
                            'unit_price'  => $service1[1],
                            'total'       => $service1[1] * $service1[2],
                        ],
                        [
                            'description' => $service2[0],
                            'quantity'    => $service2[2],
                            'unit_price'  => $service2[1],
                            'total'       => $service2[1] * $service2[2],
                        ],
                    ]);

                    // بعض الفواتير مدفوعة جزئياً أو كاملاً
                    $payScenario = rand(0, 2);
                    if ($payScenario === 0) {
                        // مدفوعة بالكامل
                        $invoice->payments()->create([
                            'patient_id'     => $patient->id,
                            'amount'         => $total,
                            'payment_method' => ['cash','card','other'][rand(0,2)],
                            'payment_date'   => $invoiceDate->toDateString(),
                        ]);
                        $invoice->update(['paid_amount' => $total]);
                    } elseif ($payScenario === 1) {
                        // مدفوعة جزئياً
                        $partial = round($total * (rand(3, 7) / 10));
                        $invoice->payments()->create([
                            'patient_id'     => $patient->id,
                            'amount'         => $partial,
                            'payment_method' => 'cash',
                            'payment_date'   => $invoiceDate->toDateString(),
                        ]);
                        $invoice->update(['paid_amount' => $partial]);
                    }
                    // payScenario 2 = مش مدفوعة خالص
                }
            }
        }

        // =====================
        // 6. PRESCRIPTIONS
        // =====================

        $meds = [
            ['أموكسيسيللين', '500 مجم', 'ثلاث مرات يومياً', '7 أيام', 'بعد الأكل'],
            ['إيبوبروفين',   '400 مجم', 'عند الألم حسب الحاجة', '5 أيام', 'مع الأكل'],
            ['ميترونيدازول', '500 مجم', 'مرتين يومياً', '5 أيام', 'بعد الأكل'],
            ['كلورهيكسيدين', 'غسول', 'مرتين يومياً', '10 أيام', 'بعد تنظيف الأسنان'],
            ['باراسيتامول',  '1 جرام', 'عند الحاجة', '3 أيام', 'يمكن مع أو بدون أكل'],
        ];

        foreach (array_slice($patients, 0, 15) as $patient) {
            if (!$patient->prescriptions()->exists()) {
                $med = $meds[rand(0, count($meds)-1)];
                $pres = $patient->prescriptions()->create([
                    'user_id'          => $patient->doctor_id ?? $dr1->id,
                    'prescription_date'=> $now->copy()->subDays(rand(1, 30))->toDateString(),
                    'diagnosis'        => 'التهاب اللثة / ما بعد إجراء طبي',
                    'notes'            => 'يراجع بعد انتهاء الجرعة',
                ]);

                $pres->items()->create([
                    'medication_name' => $med[0],
                    'dosage'          => $med[1],
                    'frequency'       => $med[2],
                    'duration'        => $med[3],
                    'instructions'    => $med[4],
                ]);
            }
        }

        $this->command->info('✅ تم إنشاء البيانات التجريبية بنجاح:');
        $this->command->info('   👥 6 مستخدمين (1 مدير، 2 استقبال، 4 دكاترة)');
        $this->command->info('   🏥 20 مريض موزعين على الدكاترة');
        $this->command->info('   📅 30 موعد بحالات مختلفة');
        $this->command->info('   💰 فواتير ومدفوعات متنوعة');
        $this->command->info('   💊 وصفات طبية لـ 15 مريض');
        $this->command->info('');
        $this->command->info('🔑 بيانات الدخول:');
        $this->command->info('   admin@dental.com       / password  (مدير)');
        $this->command->info('   sara@dental.com        / password  (استقبال)');
        $this->command->info('   nada@dental.com        / password  (استقبال)');
        $this->command->info('   dr.omar@dental.com     / password  (دكتور تقويم)');
        $this->command->info('   dr.maya@dental.com     / password  (دكتور أعصاب)');
        $this->command->info('   dr.tarek@dental.com    / password  (دكتور زراعة)');
        $this->command->info('   dr.hana@dental.com     / password  (دكتور أطفال)');
    }
}
