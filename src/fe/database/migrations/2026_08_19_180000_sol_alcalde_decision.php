<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sol_solicitudes')) {
            return;
        }
        Schema::table('sol_solicitudes', function (Blueprint $table) {
            if (!Schema::hasColumn('sol_solicitudes', 'alcalde_decision')) {
                $table->string('alcalde_decision', 20)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sol_solicitudes') || !Schema::hasColumn('sol_solicitudes', 'alcalde_decision')) {
            return;
        }
        Schema::table('sol_solicitudes', function (Blueprint $table) {
            $table->dropColumn('alcalde_decision');
        });
    }
};
