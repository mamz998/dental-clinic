<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // جدول حالة كل سن للمريض (odontogram)
        Schema::create('teeth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->integer('tooth_number'); // 1-32 للبالغين
            $table->string('tooth_type')->default('adult'); // adult / child
            $table->enum('status', [
                'healthy',
                'filling',
                'crown',
                'root_canal',
                'missing',
                'needs_extraction',
                'implant',
                'bridge',
                'other'
            ])->default('healthy');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['patient_id', 'tooth_number', 'tooth_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teeth');
    }
};
