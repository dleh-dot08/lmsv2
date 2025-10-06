<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bulk_import_stagings', function (Blueprint $table) {
            $table->id();
            
            // Kolom Audit/History
            $table->bigInteger('uploaded_by')->unsigned()->index();
            $table->string('import_type')->comment('create_assign atau grade_update');
            $table->string('batch_token')->unique(); // Token untuk mengelompokkan satu file upload
            
            // Kolom Data Mentah dari CSV (Untuk Create/Assign)
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('nisn')->nullable();
            $table->string('npsn')->nullable();
            $table->string('category')->nullable();
            $table->string('major')->nullable();
            $table->string('password')->nullable();
            $table->string('initial_grade_name')->nullable();
            $table->string('target_grade_name')->nullable(); // Untuk update
            
            // Kolom Status Validasi
            $table->string('validation_status')->default('pending')->comment('pending, success, failed');
            $table->text('validation_errors')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bulk_import_stagings');
    }
};
