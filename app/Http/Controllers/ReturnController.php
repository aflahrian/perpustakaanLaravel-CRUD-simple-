<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnRecord;// Model Return Record untuk interaksi dengan database
use App\Models\Rental;// Model Rental untuk interaksi dengan database
use App\Models\Books;// Model Rental untuk interaksi dengan database
use App\Models\Customer;// Model Rental untuk interaksi dengan database
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnRecord::with('rental')->get(); 
        return response()->json($returns);
    }


    public function show($id)
    {
        $return = ReturnRecord::with('rental')->find($id);
        if ($return) {
            return response()->json($return);
        }
        return response()->json(['message' => 'Return not found'], 404);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required|exists:rental,id',
            'returned_at' => 'required|date_format:Y-m-d',// Validasi untuk format tanggal
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400); // Mengembalikan kesalahan validasi
        }

        $rental = Rental::find($request->rental_id);
        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        if ($rental->returned_at) {
            return response()->json(['message' => 'Book has already been returned'], 400);
        }

        // Hitung denda
        $penalty = 0;
        $late = false;

        $dueDate = \Carbon\Carbon::parse($rental->borrowed_at)->addDays(7);
        // $returnedAt = \Carbon\Carbon::parse($request->returned_at);
        $returnedAt = \Carbon\Carbon::createFromFormat('Y-m-d', $request->returned_at);

        if ($returnedAt->isAfter($dueDate)) {
            $late = true;
            $daysLate = $returnedAt->diffInDays($dueDate);
            $penalty = $daysLate * 1000; // Misalnya denda Rp1000 per hari
        }

        // Perbarui status peminjaman
        $rental->returned_at = $request->returned_at;
        $rental->save();

         // Tambahkan kembali jumlah buku ke tabel books
        $book = Books::find($rental->book_id); // Mengambil buku yang terkait dengan rental
        $book->total += 1; // Menambahkan kembali total buku
        $book->save(); // Simpan perubahan

        $returnRecord = ReturnRecord::create([
            'rental_id' => $rental->id,
            'returned_at' => $request->returned_at,
            'late' => $late,
            'penalty' => $penalty,
        ]);

        return response()->json($returnRecord, 201);
    }
}
