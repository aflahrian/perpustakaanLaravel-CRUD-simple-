<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;// Untuk menggunakan Request
use App\Models\Books;// Model Books untuk interaksi dengan database
use App\Models\Rental;// Model Books untuk interaksi dengan database
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    public function index()
    {
        $books = Books::all(); // Mengambil semua data buku dari database
        return response()->json($books); // Mengembalikan data buku dalam format JSON
    }

    public function show($id)
    {
        $book = Books::find($id); // Mencari buku berdasarkan ID
        if ($book) {
            return response()->json($book); // Mengembalikan data buku jika ditemukan
        }
        return response()->json(['message' => 'Book not found'], 404); // Mengembalikan pesan jika buku tidak ditemukan
    }

    public function store(Request $request)
    {
      

        $validator = Validator::make($request->all(), [
            // 'name' => 'required|string|max:255|unique:books,name', // Nama buku harus unik
            // 'name' => 'required|string|max:255|unique:books,name,NULL,id,type,' . $request->type, // Validasi unik berdasarkan type
            'name' => 'required|string|max:255', // Validasi unik berdasarkan type
            'type' => 'required|string|max:255', // type harus diisi
            'total' => 'required|integer|min:1', //total harus lebih besar dari 0
            'price' => 'required|numeric|min:1', // Harga harus lebih besar dari 0
        ]);
          // Menambahkan validasi unik untuk kombinasi name dan type
        $validator->after(function ($validator) use ($request) {
            if (Books::where('name', $request->name)
                ->where('type', $request->type)
                ->exists()) {
                $validator->errors()->add('name', 'The combination of name and type must be unique.');
            }
        });

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 400);
        }

        $book = Books::create($request->all()); // Membuat buku baru dengan data yang diterima
        return response()->json($book, 201); // Mengembalikan data buku yang baru dibuat dengan status 201 (Created)
    }

    public function update(Request $request, $id)
    {

        $book = Books::find($id); // Mencari buku berdasarkan ID
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', // Nama buku harus diisi dan tidak lebih dari 255 karakter
            'type' => 'required|string|max:255', // Type harus diisi dan tidak lebih dari 255 karakter
            'total' => 'required|integer|min:1', // Total harus lebih besar dari 0
            'price' => 'required|numeric|min:1', // Harga harus lebih besar dari 0
        ]);
    
        // Menambahkan validasi unik untuk kombinasi name dan type
        $validator->after(function ($validator) use ($request, $id) {
            // Memeriksa apakah ada kombinasi name dan type yang sama, kecuali untuk buku yang sedang diperbarui
            if (Books::where('name', $request->name)
                ->where('type', $request->type)
                ->where('id', '!=', $id) // Mengabaikan ID yang sama
                ->exists()) {
                $validator->errors()->add('name', 'The combination of name and type must be unique, except for the current record.');
            }
        });
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400); // Mengembalikan kesalahan validasi
        }

        if ($book) {
            $book->update($request->all()); // Memperbarui data buku dengan data yang baru diterima
            return response()->json($book); // Mengembalikan data buku yang diperbarui
        }
        return response()->json(['message' => 'Book not found'], 404); // Mengembalikan pesan jika buku tidak ditemukan
    }

    public function destroy($id)
    {
        // Mengambil buku yang ingin dihapus
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found'], 404);
        }

        // Memeriksa apakah buku sedang dipinjam
        $activeRental = Rental::where('book_id', $id)
            ->whereNull('returned_at') // Cek apakah masih dalam proses peminjaman
            ->exists(); // Mengecek ada tidaknya entri

        if ($activeRental) {
            return response()->json(['message' => 'Cannot delete the book. It is currently borrowed.'], 400);
        }

        // Menghapus entri buku
        $book->delete();
        return response()->json(['message' => 'Book deleted successfully']);
    }
}
