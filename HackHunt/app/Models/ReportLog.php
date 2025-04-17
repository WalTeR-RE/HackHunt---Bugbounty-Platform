<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportLog extends Model
{
    use HasFactory;
    protected $table = 'report_logs';


    protected $fillable = [
        'report_id',
        'performed_by',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'uuid');
    }

    public function performedBy()
    {
        return $this->belongsTo(Users::class, 'performed_by', 'uuid');
    }
}
