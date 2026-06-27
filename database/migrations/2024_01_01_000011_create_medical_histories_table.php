<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            // حساسية
            $table->boolean('allergy_anesthesia')->default(false);
            $table->boolean('allergy_penicillin')->default(false);
            $table->text('allergies_other')->nullable();
            // أمراض مزمنة
            $table->boolean('has_diabetes')->default(false);
            $table->boolean('has_heart_disease')->default(false);
            $table->boolean('has_bleeding_disorder')->default(false);
            $table->text('chronic_conditions_other')->nullable();
            // أدوية
            $table->text('current_medications')->nullable();
            // حمل
            $table->boolean('is_pregnant')->default(false);
            // ملاحظات طبية عامة
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_histories');
    }
};
