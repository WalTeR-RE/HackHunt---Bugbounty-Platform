<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;

    protected $table = 'friends';

    protected $fillable = [
        'user_one_id',
        'user_two_id',
        'status'
    ];

    
    public function userOne()
    {
        return $this->belongsTo(Users::class, 'user_one_id', 'uuid');
    }

    public function userTwo()
    {
        return $this->belongsTo(Users::class, 'user_two_id', 'uuid');
    }
}
