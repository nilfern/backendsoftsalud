<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      /*  User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);*/

    // Crear el usuario admin (en la tabla users)
    $user = User::firstOrCreate(
        ['email' => 'nil@gmail.com'],
        [
            'password' => Hash::make('123'),
            'role' => 'administrador',
            'name' => 'Admin',
            'surname' => 'Principal'
        ]
    );

    // Crear el empleado relacionado (en la tabla employees)
    Employee::firstOrCreate(
        ['user_id' => $user->id],
        [
            'name' => 'Admin',
            'surname' => 'Principal',
            'dni' => '000000001',
            'occupation' => 'Administrador del sistema',
            'gross_salary' => 50000.00,
            'email' => 'admin@example.com',
            'password' => Hash::make('123'), // Recomendado aunque tal vez no uses este campo
            'genre' => 'Masculino', // Asegúrate de que este valor sea válido
            'photo' => 'employees_photos/usuario.jpg', // O una ruta por defecto si usas imágenes
            'phone' => '600000000',
            'address'=> 'carrea 8',
            'birthdate' => '1990-01-01',
            'user_id' => $user->id,
        ]
    );








    }
}
