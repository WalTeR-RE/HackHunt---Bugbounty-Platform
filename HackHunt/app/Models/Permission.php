<?php

namespace App\Models;


class Permission extends BaseModel
{
    protected $table = 'permissions';
    protected $fillable = [
        "name"
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'permission_role')
            ->withTimestamps();
    }
}
