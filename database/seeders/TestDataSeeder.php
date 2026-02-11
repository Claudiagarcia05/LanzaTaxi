<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create usuarios
        DB::table('usuarios')->insert([
            [
                'nombre' => 'Juan Cliente',
                'email' => 'juan@test.com',
                'password' => Hash::make('password123'),
                'telefono' => '922111111',
                'tipo' => 'cliente',
                'estado' => 'activo',
                'verificado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'María Taxista',
                'email' => 'maria@test.com',
                'password' => Hash::make('password123'),
                'telefono' => '922222222',
                'tipo' => 'taxista',
                'estado' => 'activo',
                'verificado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Admin Sistema',
                'email' => 'admin@test.com',
                'password' => Hash::make('password123'),
                'telefono' => '922333333',
                'tipo' => 'admin',
                'estado' => 'activo',
                'verificado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create clientes
        DB::table('clientes')->insert([
            [
                'usuario_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create vehiculos
        DB::table('vehiculos')->insert([
            [
                'marca' => 'Toyota',
                'modelo' => 'Prius',
                'matricula' => '1234ABC',
                'color' => 'Blanco',
                'seats' => 4,
                'licencia' => 'ABC123',
                'seguro_until' => now()->addYear(),
                'itv_until' => now()->addYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create taxistas
        DB::table('taxistas')->insert([
            [
                'usuario_id' => 2,
                'licencia_nro' => 'TAXI123',
                'vehiculo_id' => 1,
                'estado' => 'disponible',
                'ubicacion_lat' => 29.0339,
                'ubicacion_lng' => -13.6431,
                'calificacion' => 4.8,
                'municipio' => 'Arrecife',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create tarifas
        DB::table('tarifas')->insert([
            [
                'nombre' => 'Tarifa Urbana',
                'tipo' => 'urbano',
                'bajada_bandera' => 3.50,
                'precio_km' => 1.20,
                'precio_espera_hora' => 20.00,
                'minimo_viaje' => 5.00,
                'activa' => true,
                'vigente_desde' => now(),
                'vigente_hasta' => now()->addMonths(6),
                'municipios' => json_encode(['Arrecife', 'Tías', 'San Bartolomé']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarifa Interurbana',
                'tipo' => 'interurbano',
                'bajada_bandera' => 5.00,
                'precio_km' => 1.50,
                'precio_espera_hora' => 25.00,
                'minimo_viaje' => 8.00,
                'activa' => true,
                'vigente_desde' => now(),
                'vigente_hasta' => now()->addMonths(6),
                'municipios' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Tarifa Especial',
                'tipo' => 'especial',
                'bajada_bandera' => 10.00,
                'precio_km' => 2.00,
                'precio_espera_hora' => 30.00,
                'minimo_viaje' => 15.00,
                'activa' => true,
                'vigente_desde' => now(),
                'vigente_hasta' => now()->addMonths(6),
                'municipios' => json_encode(['Aeropuerto']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create viajes
        DB::table('viajes')->insert([
            [
                'cliente_id' => 1,
                'taxista_id' => 2,
                'origen_lat' => 29.0339,
                'origen_lng' => -13.6431,
                'origen_direccion' => 'Calle Principal, Arrecife',
                'destino_lat' => 29.0500,
                'destino_lng' => -13.6500,
                'destino_direccion' => 'Calle Secundaria, Tías',
                'distancia_km' => 5.5,
                'tiempo_estimado' => 12,
                'precio' => 18.50,
                'tarifa_id' => 1,
                'estado' => 'completado',
                'ocupantes' => 1,
                'comentarios' => 'Viaje de prueba',
                'calificaciones' => null,
                'metodo_pago' => 'tarjeta',
                'pagado' => 1,
                'created_at' => now()->subHour(),
                'updated_at' => now(),
            ],
        ]);

        // Create evaluaciones
        DB::table('evaluaciones')->insert([
            [
                'viaje_id' => 1,
                'evaluador_id' => 1,
                'evaluado_id' => 2,
                'calificacion' => 5,
                'comentario' => 'Muy buen servicio, conductor amable',
                'aspecto' => 'En general',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create transacciones
        DB::table('transacciones')->insert([
            [
                'viaje_id' => 1,
                'usuario_id' => 1,
                'tipo' => 'pago',
                'monto' => 18.50,
                'metodo' => 'tarjeta',
                'estado' => 'completado',
                'referencia' => 'VIAJE_001_PAGO',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'viaje_id' => 1,
                'usuario_id' => 2,
                'tipo' => 'pago',
                'monto' => 15.73,
                'metodo' => 'wallet',
                'estado' => 'completado',
                'referencia' => 'VIAJE_001_INGRESO',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create notificaciones
        DB::table('notificaciones')->insert([
            [
                'usuario_id' => 1,
                'viaje_id' => 1,
                'tipo' => 'viaje_completado',
                'titulo' => 'Viaje completado',
                'mensaje' => 'Tu viaje ha sido completado. Por favor, califica al taxista.',
                'leida' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'usuario_id' => 2,
                'viaje_id' => 1,
                'tipo' => 'viaje_completado',
                'titulo' => 'Viaje completado',
                'mensaje' => 'Viaje completado exitosamente. Ganancia: €15.73',
                'leida' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'usuario_id' => 3,
                'viaje_id' => null,
                'tipo' => 'sistema',
                'titulo' => 'Bienvenida al sistema',
                'mensaje' => 'Bienvenido a LanzaTaxi',
                'leida' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Create admins
        DB::table('admins')->insert([
            [
                'usuario_id' => 3,
                'permisos' => json_encode(['users_manage', 'taxistas_manage', 'viajes_view', 'tarifas_edit', 'comisiones_view', 'reportes_view', 'sistema_config']),
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
