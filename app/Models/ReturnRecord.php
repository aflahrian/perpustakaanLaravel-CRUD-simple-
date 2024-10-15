<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRecord extends Model
{
    use HasFactory;


    protected $table = 'return';

    protected $primaryKey = 'id';

    public $incrementing = true;

    public $timestamps = true;

    protected $fillable = ['rental_id','returned_at','late','penalty','id'];

    public function rental() {
        return $this->belongsTo(Rental::class, 'rental_id', 'id');
    }
}
