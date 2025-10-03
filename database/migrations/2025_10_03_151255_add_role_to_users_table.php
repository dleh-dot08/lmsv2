<?php

// database/migrations/...add_role_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom role string yang lama (jika ada)
            // $table->dropColumn('role'); 

            // Tambahkan foreign key role_id
            $table->foreignId('role_id')
                ->default(4) // Default role Peserta (ID 4)
                ->constrained('roles')
                ->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};