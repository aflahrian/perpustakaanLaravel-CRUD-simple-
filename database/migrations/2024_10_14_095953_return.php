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
        Schema::create('return', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('id')->constrained('loans')->onDelete('cascade'); // ID peminjaman yang terhubung dengan tabel loans
            $table->foreignId('rental_id') // Kolom book_id yang merujuk ke tabel books
            ->constrained('rental', 'id') // Menghubungkan ke kolom id di tabel books
            ->onDelete('cascade'); // Menghapus pengembalian jika buku dihapus
            $table->timestamp('returned_at')->nullable(); // Tanggal pengembalian
            $table->integer('late')->nullable();
            $table->decimal('penalty', 20, 8)->nullable();
            $table->timestamps();//for update and create
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('return');
    }
};
