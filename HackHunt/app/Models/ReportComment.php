<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportComment extends Model
{
    use HasFactory;
    protected $table = 'report_comments';


    protected $fillable = [
        'report_id',
        'user_id',
        'comment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'uuid');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id', 'uuid');
    }
}
