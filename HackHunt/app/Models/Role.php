<?php

namespace App\Models;


class Role extends BaseModel {
    protected $fillable = [
        'id',
        'name',
        'description'
    ];

    public function permissions() {
        return $this->belongsToMany(Permission::class, 'permission_role')
            ->withTimestamps();
    }
}
