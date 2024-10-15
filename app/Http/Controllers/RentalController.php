<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;// Model Rental untuk interaksi dengan database
use App\Models\Books;// Model Rental untuk interaksi dengan database
use App\Models\Customer;// Model Rental untuk interaksi dengan database
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['book', 'customer'])->get(); 
        return response()->json($rentals);
    }

    public function show($id)
    {
        // Menampilkan detail rental berdasarkan ID yang diberikan
        $rental = Rental::with(['book', 'customer'])->find($id);
        if ($rental) {
            return response()->json($rental);
        }
        return response()->json(['message' => 'Rental not found'], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id', // Validasi buku yang valid
            'customer_id' => 'required|exists:customer,id', // Validasi pelanggan yang valid
            'borrowed_at' => 'required|date_format:Y-m-d', // Tanggal peminjaman harus valid
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400); // Mengembalikan kesalahan validasi
        }

        // Memeriksa apakah customer sudah meminjam buku yang sama
        $activeRental = Rental::where('customer_id', $request->customer_id)
            ->where('book_id', $request->book_id)
            ->whereNull('returned_at') // Cek apakah masih dalam proses peminjaman
            ->first();

        if ($activeRental) {
            return response()->json(['message' => 'Customer has already borrowed this book and has not returned it yet.'], 400);
        }

        // Ambil buku terkait untuk memeriksa total
        $book = Books::find($request->book_id);

        // Validasi untuk memastikan total buku lebih dari 0
        if ($book->total < 1) {
            return response()->json(['message' => 'No available books to borrow'], 400);
        }
         // Mengurangi total buku saat peminjaman dilakukan
        $book->total -= 1;
        $book->save();

        // Mengonversi borrowed_at ke objek Carbon sebelum menyimpan
        $request->merge(['borrowed_at' => \Carbon\Carbon::createFromFormat('Y-m-d', $request->borrowed_at)]);
        $rental = Rental::create($request->all());// Membuat entri rental baru
        return response()->json($rental, 201); // Mengembalikan data rental yang baru dibuat
    

    }
}
