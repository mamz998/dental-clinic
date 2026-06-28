<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('specialty')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0); // نسبة مئوية 0-100
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // إضافة doctor_id لجدول المرضى
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
        });

        // إضافة doctor_id للمواعيد
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete()->after('patient_id');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'doctor_id');
            $table->dropColumn('doctor_id');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class, 'doctor_id');
            $table->dropColumn('doctor_id');
        });
        Schema::dropIfExists('doctors');
    }
};
