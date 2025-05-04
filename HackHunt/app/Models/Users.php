<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;


class Users extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'users';

    protected $fillable = [
        'uuid',
        'name',
        'about_me',
        'nickname',
        'profile_picture',
        'background_picture',
        'role_id',
        'rank',
        'country',
        'active',
        'total_points',
        'accuracy',
        'links',
        'email',
        'phone_number',
        'birthday',
        'email_verified_at',
        'password',
        'verified',
        'vulnerabilities_count',
        'engagement_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'authenticated'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'links' => 'array',
        'active' => 'boolean',
        'verified' => 'boolean',
        'authenticated' => 'boolean'
    ];

    public function getRememberToken()
    {
        $session = DB::table('sessions')->where('user_id', $this->uuid)->latest('last_activity')->first();
        return $session ? $session->remember_token : null;
    }

    public function IsLoggedIn(){
        return $this->authenticated;
    }

    public function setRememberToken($value)
    {
        DB::table('sessions')
            ->updateOrInsert(
                ['user_id' => $this->uuid],
                ['remember_token' => $value, 'last_activity' => now()->timestamp]
            );
    }
    public function role() {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function hasPermission($permissionName)
    {
        return $this->role->permissions->contains('name', $permissionName);
    }

    public function ownedPrograms()
    {
    return $this->belongsToMany(Program::class, 'program_user', 'user_id', 'program_id')->select('uuid','name');
                
    }



    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public static function validationRules($isUpdate = false, $userId = null)
{
    return [
        'name' => $isUpdate ? 'nullable|string|max:255' : 'required|string|max:255',
        'about_me' => 'nullable|string|max:500',
        'nickname' => 'required|string|max:50|unique:users,nickname' . ($isUpdate ? ",$userId,uuid" : ''),
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'background_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'role_id' => 'nullable|integer|min:1',
        'rank' => 'nullable|integer|min:0',
        'country' => 'required|string|max:100',
        'active' => 'boolean',
        'total_points' => 'integer|min:0',
        'accuracy' => 'numeric|min:0|max:100',
        'links' => 'nullable|array',
        'email' => 'required|email|max:255|unique:users,email' . ($isUpdate ? ",$userId,uuid" : ''),
        'phone_number' => 'required|string|max:20|unique:users,phone_number' . ($isUpdate ? ",$userId,uuid" : ''),
        'birthday' => $isUpdate ? 'nullable|date|before:today' : 'required|date|before:today',
        'password' => $isUpdate ? 'nullable|string|min:8' : 'required|string|min:8',
        'verified' => 'boolean',
        'vulnerabilities_count' => 'integer|min:0|nullable',
        'engagement_count' => 'integer|min:0|nullable',
    ];
}

}
