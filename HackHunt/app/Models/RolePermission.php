<?php

namespace App\Models;


class RolePermission extends BaseModel
{
    protected $table = "permission_role";
    protected $fillable = [
        'role_id',
        'permission_id'
    ];
}
