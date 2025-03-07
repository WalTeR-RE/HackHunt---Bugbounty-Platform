<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'view_users'],
            ['id' => 2, 'name' => 'edit_users'],
            ['id' => 3, 'name' => 'delete_users'],
            ['id' => 4, 'name' => 'create_users'],
            ['id' => 5, 'name' => 'manage_roles'],
            ['id' => 6, 'name' => 'Test'],
        ]);
    }
}
