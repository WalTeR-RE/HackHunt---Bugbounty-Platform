<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $primaryKey = 'program_id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'program_id',
        'name',
        'bounty_range',
        'is_private',
        'number_of_reports',
        'avg_bounty',
        'validation_time',
        'vulnerabilities_rewarded',
        'started_at',
        'fast_description',
        'rewards',
        'target_description',
        'scope',
        'description_rules',
        'status',
        'Owners_id'
    ];

    protected $casts = [
        'rewards' => 'array',
        'scope' => 'array',
        'Owners_id'=>'array',

    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'program_id', 'program_id');
    }

    public function totalBounty()
    {
        return $this->reports()->sum('bounty');
    }
}
