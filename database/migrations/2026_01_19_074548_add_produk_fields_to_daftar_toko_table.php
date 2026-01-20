<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->string('gambar_produk')->nullable()->after('harga_produk');
    });
}

public function down()
{
    Schema::table('daftar_toko', function (Blueprint $table) {
        $table->dropColumn('gambar_produk');
    });
}


};
