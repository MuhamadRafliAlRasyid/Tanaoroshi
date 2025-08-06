<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_part');
            $table->string('model');
            $table->string('merk');
            $table->integer('jumlah_baru')->default(0);
            $table->integer('jumlah_bekas')->default(0);
            $table->string('supplier');
            $table->decimal('patokan_harga', 12, 2);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('ruk_no');
            $table->date('purchase_date');
            $table->date('delivery_date');
            $table->string('po_number');
            $table->string('titik_pesanan');
            $table->integer('jumlah_pesanan');
            $table->boolean('cek')->default(false);
            $table->string('pic');
            $table->text('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};
