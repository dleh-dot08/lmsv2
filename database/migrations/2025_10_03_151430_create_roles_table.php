<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // ID 1 sampai 6
            $table->string('name')->unique(); // Nama peran: Super admin, admin, dll.
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};