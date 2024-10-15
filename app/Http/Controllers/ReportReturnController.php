<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;// Model Rental untuk interaksi dengan database
use App\Models\ReturnRecord;// Model Return Record untuk interaksi dengan database
use App\Models\Books;// Model Rental untuk interaksi dengan database
use App\Models\Customer;// Model Rental untuk interaksi dengan database
use Illuminate\Support\Facades\DB;

class ReportReturnController extends Controller
{
    public function index() {
        //cara 1
        // $rental = Rental::select('id','borrowed_at','returned_at','book_id')
        // ->with(['book' => function ($query) {
        //     $query->select('id','name');
        // }])
        // ->get();
        
        //cara 2 Menggunakan Eager Loading yang Lebih Efisien
        // Mengambil semua data return dengan relasi rental, book, dan customer
        $returnx = ReturnRecord::with(['rental.book:id,name', 'rental.customer:id,name'])
        ->select('id', 'rental_id', 'returned_at', 'late', 'penalty') // Memilih kolom yang diinginkan dari tabel return
        ->get();

        // Format output untuk menampilkan data
        $returns = $returnx->map(function ($return) {
            return [
                'return_id' => $return->id,
                'rental_id' => $return->rental_id,
                'borrowed_at' => $return->rental->borrowed_at, // Mengambil borrowed_at dari rental
                'returned_at' => $return->returned_at,
                'late' => $return->late,
                'penalty' => $return->penalty,
                'book_name' => $return->rental->book->name, // Mengambil nama buku
                'customer_name' => $return->rental->customer->name // Mengambil nama pelanggan
            ];
        });


        // cara 3 Query Builder yang Optimal
        // $returns = DB::table('return')
        // ->join('rental', 'return.rental_id', '=', 'rental.id')
        // ->join('books', 'rental.book_id', '=', 'books.id')
        // ->join('customer', 'rental.customer_id', '=', 'customer.id')
        // ->select('return.id as return_id',
        //         'rental.id as rental_id', 
        //         'rental.borrowed_at as borrowed_at', 
        //         'return.late as late', 
        //         'return.penalty as penalty', 
        //         'return.returned_at as returned_at', 
        //         'books.name as book_name', 
        //         'customer.name as customer_name')
        // ->get();

        return response()->json(['rental' => $returns]);
    }
}
