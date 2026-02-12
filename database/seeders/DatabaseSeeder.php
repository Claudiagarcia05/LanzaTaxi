<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Taxista;
use App\Models\Tarifa;
use App\Models\Viaje;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuarios
        $admin = User::create([
            'email' => 'admin@test.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'nombre' => 'Administrador Principal',
            'telefono' => '928123456',
        ]);

        $cliente1 = User::create([
            'email' => 'cliente@test.com',
            'password' => Hash::make('123456'),
            'role' => 'cliente',
            'nombre' => 'María García',
            'telefono' => '628111222',
        ]);

        $cliente2 = User::create([
            'email' => 'cliente2@test.com',
            'password' => Hash::make('123456'),
            'role' => 'cliente',
            'nombre' => 'John Smith',
            'telefono' => '628222333',
        ]);

        $taxista1User = User::create([
            'email' => 'taxista@test.com',
            'password' => Hash::make('123456'),
            'role' => 'taxista',
            'nombre' => 'Carlos Rodríguez',
            'telefono' => '628333444',
        ]);

        $taxista2User = User::create([
            'email' => 'taxista2@test.com',
            'password' => Hash::make('123456'),
            'role' => 'taxista',
            'nombre' => 'Pedro Martínez',
            'telefono' => '628444555',
        ]);

        $taxista3User = User::create([
            'email' => 'taxista3@test.com',
            'password' => Hash::make('123456'),
            'role' => 'taxista',
            'nombre' => 'Ana López',
            'telefono' => '628555666',
        ]);

        // Crear taxistas
        $taxista1 = Taxista::create([
            'user_id' => $taxista1User->id,
            'licencia' => 'LZ-001',
            'municipio' => 'Arrecife',
            'matricula' => '1234-ABC',
            'modelo_vehiculo' => 'Toyota Prius',
            'estado' => 'libre',
            'latitud' => 28.945,
            'longitud' => -13.605,
        ]);

        $taxista2 = Taxista::create([
            'user_id' => $taxista2User->id,
            'licencia' => 'LZ-002',
            'municipio' => 'Teguise',
            'matricula' => '5678-DEF',
            'modelo_vehiculo' => 'Mercedes E-Class',
            'estado' => 'libre',
            'latitud' => 29.060,
            'longitud' => -13.562,
        ]);

        $taxista3 = Taxista::create([
            'user_id' => $taxista3User->id,
            'licencia' => 'LZ-003',
            'municipio' => 'Tías',
            'matricula' => '9012-GHI',
            'modelo_vehiculo' => 'Seat Toledo',
            'estado' => 'ocupado',
            'latitud' => 28.927,
            'longitud' => -13.664,
        ]);

        // Crear tarifas
        Tarifa::create([
            'nombre' => 'Tarifa 1 - Urbana',
            'bajada_bandera' => 3.15,
            'precio_km' => 0.60,
            'suplemento_aeropuerto' => 3.50,
            'suplemento_puerto' => 2.00,
            'suplemento_nocturno' => 0.20,
            'suplemento_festivo' => 0.30,
            'activa' => true,
        ]);

        Tarifa::create([
            'nombre' => 'Tarifa 2 - Interurbana',
            'bajada_bandera' => 3.15,
            'precio_km' => 0.75,
            'suplemento_aeropuerto' => 3.50,
            'suplemento_puerto' => 2.00,
            'suplemento_nocturno' => 0.20,
            'suplemento_festivo' => 0.30,
            'activa' => true,
        ]);

        // Crear viaje de ejemplo
        Viaje::create([
            'cliente_id' => $cliente1->id,
            'taxista_id' => $taxista1->id,
            'origen_lat' => 28.945,
            'origen_lng' => -13.605,
            'origen_direccion' => 'Aeropuerto de Lanzarote',
            'destino_lat' => 28.963,
            'destino_lng' => -13.547,
            'destino_direccion' => 'Arrecife, Calle León y Castillo',
            'distancia' => 8.5,
            'precio_estimado' => 12.60,
            'precio_final' => 12.60,
            'estado' => 'finalizado',
            'tipo_tarifa' => 'Tarifa 2',
            'valoracion' => 5,
            'comentario' => 'Muy puntual y amable',
            'fecha_solicitud' => now()->subHours(2),
            'fecha_fin' => now()->subHours(1),
        ]);

        $this->command->info('✅ Datos de prueba creados correctamente');
    }
}

