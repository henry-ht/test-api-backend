<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $datos = [
            array(
                'name'          => 'super_admin',
                'description'   => '',
            ),
            array(
                'name'          => 'app_admin',
                'description'   => '',
            ),
            array(
                'name'          => 'app_user',
                'description'   => '',
            ),
        ];

        foreach ($datos as $key => $value) {
            Role::updateOrCreate([
                'name' => $value['name']
            ], [
                'description' => $value['description']
            ]);
        }
    }
}
