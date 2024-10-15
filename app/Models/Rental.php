<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $table = 'rental';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['book_id','customer_id','borrowed_at','returned_at','id'];

    public function book() {
        return $this->belongsTo(Books::class, 'book_id', 'id');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function return() {
        return $this->hasMany(ReturnRecord::class,'rental_id','id');
    }
}
