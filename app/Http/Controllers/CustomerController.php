<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;// Model Books untuk interaksi dengan database
use App\Models\Rental;// Model Books untuk interaksi dengan database
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json($customers);
    }

    public function show($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            return response()->json($customer);
        }
        return response()->json(['message' => 'Customer not found'], 404);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255', // Nama pelanggan harus diisi dan tidak lebih dari 255 karakter
            'age' => 'required|integer|min:19', // Usia harus diisi dan harus berupa angka
            'address' => 'required|string|max:255', // Alamat harus diisi dan tidak lebih dari 255 karakter
            'email' => 'required|email|max:255|unique:customer,email', // Email harus unik
            'phone_number' => 'required|string|between:12,13|regex:/^08/|unique:customer,phone_number', // Nomor telepon harus unik
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400); // Mengembalikan kesalahan validasi
        }
    

        $customer = Customer::create($request->all());
        return response()->json($customer, 201);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255', // Nama pelanggan harus diisi dan tidak lebih dari 255 karakter
                'age' => 'required|integer|min:19',// Usia harus lebih dari 18
                'address' => 'required|string|max:255', // Alamat harus diisi dan tidak lebih dari 255 karakter
                'email' => 'required|email|max:255', // Email harus diisi dan valid
                'phone_number' => 'required|string|between:12,13|regex:/^08/', // Nomor telepon harus diisi dan tidak lebih dari 15 karakter
            ]);
        
            // Menambahkan validasi unik untuk email dan phone_number
            $validator->after(function ($validator) use ($request, $id) {
                // Memeriksa apakah ada email yang sama, kecuali untuk pelanggan yang sedang diperbarui
                if (Customer::where('email', $request->email)
                    ->where('id', '!=', $id) // Mengabaikan ID yang sama
                    ->exists()) {
                    $validator->errors()->add('email', 'The email has already been taken.');
                }
        
                // Memeriksa apakah ada phone_number yang sama, kecuali untuk pelanggan yang sedang diperbarui
                if (Customer::where('phone_number', $request->phone_number)
                    ->where('id', '!=', $id) // Mengabaikan ID yang sama
                    ->exists()) {
                    $validator->errors()->add('phone_number', 'The phone number has already been taken.');
                }
            });
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400); // Mengembalikan kesalahan validasi
            }
            $customer->update($request->all());
            return response()->json($customer);
        }
        return response()->json(['message' => 'Customer not found'], 404);
    }

    public function destroy($id)
    {
        // Mengambil customer yang ingin dihapus
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Memeriksa apakah customer memiliki rental aktif
        $activeRental = Rental::where('customer_id', $id)
            ->whereNull('returned_at') // Cek apakah masih dalam proses peminjaman
            ->exists(); // Mengecek ada tidaknya entri

        if ($activeRental) {
            return response()->json(['message' => 'Cannot delete the customer. They have active rentals.'], 400);
        }

        // Menghapus entri customer
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
        
    }
}
