<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'logo',
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
        'status'
    ];

    protected $casts = [
        'rewards' => 'array',
        'scope' => 'array',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class, 'program_id', 'program_id');
    }

    public function totalBounty()
    {
        return $this->reports()->sum('bounty');
    }

    public function totalReports()
    {
        return $this->reports()->count();
    }
    public function totalVulnerabilities()
    {
        return $this->reports()->where('status', '!=', 'N/A')->where('status','!=','Duplicate')->count();
    }

    public function totalVulnerabilitiesRewarded()
    {
        return $this->reports()->where('status', '!=', 'N/A')->where('status','!=','Duplicate')->where('bounty', '>', 0)->count();
    }
        
    
    public function owners()
    {
        return $this->belongsToMany(Users::class, 'program_user', 'program_id', 'user_id')
                    ->select('uuid','name','role_id');
    }
    
    public function insertProgramUser($userId)
    {
        return $this->owners()->attach($userId);
    }
    public function removeProgramUser($userId)
    {
        return $this->owners()->detach($userId);
    }
    
    public function getRouteKeyName()
    {
    return 'program_id';
    }
    

}
