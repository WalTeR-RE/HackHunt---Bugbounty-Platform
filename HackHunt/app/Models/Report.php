<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid'; 
    protected $keyType = 'string';
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

    public function getReportData()
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
            'severity' => $this->severity,
            'status' => $this->status,
            'bounty' => $this->bounty,
            'rewarded' => $this->rewarded,
            'points' => $this->points,
            'attachments' => $this->attachments,
            'triaged_at' => $this->triaged_at?->toDateTimeString(),
            'resolved_at' => $this->resolved_at?->toDateTimeString(),
            'published' => $this->published,
    
            'reporter' => $this->reporterUser ? [
                'uuid' => $this->reporterUser->uuid,
                'name' => $this->reporterUser->name,
                'email' => $this->reporterUser->email,
            ] : null,
    
            'program' => $this->program ? [
                'uuid' => $this->program->program_id,
                'name' => $this->program->name,
            ] : null,
    
            'comments' => $this->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'created_at' => $comment->created_at->toDateTimeString(),
                    'user' => [
                        'uuid' => $comment->user->uuid ?? null,
                        'name' => $comment->user->name ?? 'Unknown',
                    ],
                ];
            }),
    
            'logs' => $this->logs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'performed_by' => $log->performed_by,
                    'created_at' => $log->created_at->toDateTimeString(),
                ];
            }),
        ];
    }
    

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'program_id');
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
