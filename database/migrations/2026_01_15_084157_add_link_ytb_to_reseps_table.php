<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->string('link_ytb')->nullable()->after('steps');
        });
    }

    public function down(): void
    {
        Schema::table('reseps', function (Blueprint $table) {
            $table->dropColumn('link_ytb');
        });
    }
};
