<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;// Model Rental untuk interaksi dengan database
use App\Models\Books;// Model Rental untuk interaksi dengan database
use App\Models\Customer;// Model Rental untuk interaksi dengan database
use Illuminate\Support\Facades\DB;

class ReportRentalController extends Controller
{
    public function index() {
        //cara 1
        // $rental = Rental::select('id','borrowed_at','returned_at','book_id')
        // ->with(['book' => function ($query) {
        //     $query->select('id','name');
        // }])
        // ->get();
        
        //cara 2 Menggunakan Eager Loading yang Lebih Efisien
        // $rental = Rental::with(['book:id,name', 'customer:id,name']) // Menggunakan eager loading
        // ->select('id', 'borrowed_at', 'returned_at', 'book_id', 'customer_id') // Memilih kolom yang diinginkan
        // ->get();

        // // Format output jika ingin mengubah struktur
        // $rentalData = $rental->map(function ($r) {
        //     return [
        //         'id' => $r->id,
        //         'borrowed_at' => $r->borrowed_at,
        //         'returned_at' => $r->returned_at,
        //         'book_name' => $r->book->name ?? null, // Mengambil nama buku
        //         'customer_name' => $r->customer->name ?? null // Mengambil nama pelanggan
        //     ];
        // });

        // cara 3 Query Builder yang Optimal
        $rentals = DB::table('rental')
        ->join('books', 'rental.book_id', '=', 'books.id')
        ->join('customer', 'rental.customer_id', '=', 'customer.id')
        ->select('rental.id', 'rental.borrowed_at', 'rental.returned_at', 'books.name as book_name', 'customer.name as customer_name')
        ->get();

        return response()->json(['rental' => $rentals]);
    }
}
