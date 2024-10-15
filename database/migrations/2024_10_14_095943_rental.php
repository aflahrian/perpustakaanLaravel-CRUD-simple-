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
        Schema::create('rental', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id') // Kolom book_id yang merujuk ke tabel books
            ->constrained('books', 'id') // Menghubungkan ke kolom id di tabel books
            ->onDelete('cascade'); // Menghapus pengembalian jika buku dihapus
            $table->foreignId('customer_id') // Kolom book_id yang merujuk ke tabel books
            ->constrained('customer', 'id') // Menghubungkan ke kolom id di tabel books
            ->onDelete('cascade'); // Menghapus pengembalian jika buku dihapus
            $table->timestamp('borrowed_at')->nullable(); // Tanggal pinjam
            $table->timestamp('returned_at')->nullable(); // Tanggal pengembalian
            $table->timestamps();//for update and create
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('rental');
    }
};
