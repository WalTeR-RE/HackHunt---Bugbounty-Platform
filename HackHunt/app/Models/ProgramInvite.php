<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramInvite extends Model
{
    use HasFactory;

    protected $table = 'program_invites';

    protected $fillable = [
        'program_id',
        'user_id',
        'status',
        'invited_by',
        'expire_at'
    ];
    protected $primaryKey = 'program_id';
    protected $keyType = 'string';

    public $incrementing = false;

    protected $casts = [
        'expire_at' => 'datetime', 
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'uuid');
    }
}
