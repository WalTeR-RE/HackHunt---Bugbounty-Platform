<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HallOfFame extends Model
{
    use HasFactory;

    protected $table = 'hall_of_fame';

    protected $fillable = [
        'program_id',
        'user_id',
        'rank',
        'points',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'uuid');
    }
}
