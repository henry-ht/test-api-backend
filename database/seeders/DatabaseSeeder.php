<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);
        $this->call(CategorySeeder::class);

        if(App::Environment() === 'local'){
            \App\Models\User::factory()->create([
                'name'      => 'Admin test User',
                'email'     => 'admin@example.com',
                'role_id'   => Role::where('name', 'app_user')->first()->id
            ]);

            \App\Models\User::factory()->create([
                'name'      => 'Test User',
                'email'     => 'test@example.com',
                'role_id'   => Role::where('name', 'app_user')->first()->id
            ]);
        }

        if(App::Environment() === 'production'){
            \App\Models\User::updateOrCreate([
                'name'      => 'henry torres',
                'email'     => 'henrylapps2@gmail.com',
                'password'  => Hash::make('app-345_bw__45'),
                'role_id'   => Role::where('name', 'app_admin')->first()->id
            ]);
        }

    }
}
