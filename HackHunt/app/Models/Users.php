<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;



class Users extends Authenticatable
{
    use HasFactory, Notifiable;

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
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'links' => 'array',
        'active' => 'boolean',
        'verified' => 'boolean',
    ];

    public function getRememberToken()
    {
        $session = DB::table('sessions')->where('user_id', $this->uuid)->latest('last_activity')->first();
        return $session ? $session->remember_token : null;
    }

    public function setRememberToken($value)
    {
        DB::table('sessions')
            ->updateOrInsert(
                ['user_id' => $this->uuid],
                ['remember_token' => $value, 'last_activity' => now()->timestamp]
            );
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    public static function validationRules($isUpdate = false, $userId = null)
    {
        return [
            'name' => 'required|string|max:255',
            'about_me' => 'nullable|string|max:500',
            'nickname' => 'required|string|max:50|unique:users,nickname' . ($isUpdate ? ",$userId" : ''),
            'profile_picture' => 'string|nullable',
            'background_picture' => 'string|nullable',
            'role_id' => 'integer|min:-1',
            'rank' => 'integer|min:0',
            'country' => 'required|string|max:100',
            'active' => 'boolean',
            'total_points' => 'integer|min:0',
            'accuracy' => 'numeric|min:0|max:100',
            'links' => 'nullable|array',
            'email' => 'required|email|max:255|unique:users,email' . ($isUpdate ? ",$userId" : ''),
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number' . ($isUpdate ? ",$userId" : ''),
            'birthday' => 'required|date|before:today',
            'password' => $isUpdate ? 'nullable|string|min:8' : 'required|string|min:8',
            'verified' => 'boolean',
            'vulnerabilities_count' => 'integer|min:0|nullable',
            'engagement_count' => 'integer|min:0|nullable',
        ];
    }
}
