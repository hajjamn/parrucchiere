<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /* User::factory()->admin()->create();

        User::factory()->count(5)->create(); */

        $admin = [
            'first_name' => 'Domenico',
            'last_name' => 'Corcione',
            'email' => 'domenico.corcione82@gmail.com',
            'role' => 'admin',
            'email_verified_at' => Carbon::now()
        ];

        $users = [
            [
                'first_name' => 'Tonia',
                'last_name' => 'Paolella',
                'email' => 'dolcetonia95@gmail.com',
                'role' => 'user',
                'email_verified_at' => Carbon::now()
            ],
            [
                'first_name' => 'Rosa',
                'last_name' => 'Sposito',
                'email' => 'spositorosa18@gmail.com',
                'role' => 'user',
                'email_verified_at' => Carbon::now()
            ],
            [
                'first_name' => 'Susy',
                'last_name' => 'Romano',
                'email' => 'romanosusy72@gmail.com',
                'role' => 'user',
                'email_verified_at' => Carbon::now()
            ],
            [
                'first_name' => 'Francesca',
                'last_name' => 'Hadzovic',
                'email' => 'fancesca.hadzovic@gmail.com',
                'role' => 'user',
                'email_verified_at' => Carbon::now()
            ]
        ];

        $password = Hash::make('1234');

        $admin['password'] = $password;
        User::create($admin);

        foreach ($users as $user) {
            $user['password'] = $password;
            User::create($user);
        }
    }
}
