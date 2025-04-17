<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'uuid',
        'reporter',
        'program_id',
        'severity',
        'status',
        'bounty',
        'rewarded',
        'points',
        'title',
        'type',
        'description',
        'attachments',
        'triaged_at',
        'resolved_at',
        'published',
    ];

    protected $casts = [
        'attachments' => 'array',
        'triaged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'rewarded' => 'boolean',
        'published' => 'boolean',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(ReportComment::class, 'report_id', 'uuid');
    }

    public function logs()
    {
        return $this->hasMany(ReportLog::class, 'report_id', 'uuid');
    }

    public function reporterUser()
    {
        return $this->belongsTo(Users::class, 'reporter', 'uuid');
    }

    public static function getPublishedReports()
    {
        return self::whereHas('report', function($query) {
            $query->where('published', true);
        })->get();
    }
}
