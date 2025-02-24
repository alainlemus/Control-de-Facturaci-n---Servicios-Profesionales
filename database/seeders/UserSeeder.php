<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Asegúrate de que estás utilizando el modelo correcto para tu tabla de usuarios

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Alain Lemus Muñoz',
            'email' => 'alainttlm@gmail.com',
            'password' => bcrypt('timoboll'), // La contraseña se encripta antes de guardarla
        ]);
    }
}
