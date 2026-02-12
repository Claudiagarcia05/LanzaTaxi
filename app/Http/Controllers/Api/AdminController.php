<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Taxista;
use App\Models\User;
use App\Models\Viaje;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    private function ensureAdmin()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json(['error' => 'Acceso denegado'], 403);
        }

        return null;
    }

    public function dashboard()
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $hoy = now()->toDateString();

        $viajesHoyQuery = Viaje::whereDate('fecha_solicitud', $hoy);
        $viajesHoyTotal = (clone $viajesHoyQuery)->count();
        $viajesHoyIngresos = (clone $viajesHoyQuery)
            ->sum(DB::raw('COALESCE(precio_final, precio_estimado)'));

        $viajesPorHora = Viaje::selectRaw('HOUR(fecha_solicitud) as hora, COUNT(*) as total')
            ->whereDate('fecha_solicitud', $hoy)
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        $taxistasEstado = Taxista::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get();

        $usuarios = User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->get();

        $statsViajes = Viaje::selectRaw('taxista_id, COUNT(*) as total_viajes, SUM(COALESCE(precio_final, precio_estimado)) as ingresos')
            ->whereNotNull('taxista_id')
            ->groupBy('taxista_id')
            ->get()
            ->keyBy('taxista_id');

        $topTaxistas = Taxista::with('user')->get()->map(function ($taxista) use ($statsViajes) {
            $stat = $statsViajes->get($taxista->id);

            return [
                'nombre' => $taxista->user ? $taxista->user->nombre : 'N/A',
                'licencia' => $taxista->licencia,
                'total_viajes' => $stat ? (int) $stat->total_viajes : 0,
                'ingresos' => $stat ? (float) $stat->ingresos : 0.0,
                'valoracion_media' => $taxista->valoracion_media,
            ];
        })->sortByDesc('total_viajes')->values()->take(5);

        return response()->json([
            'viajesHoy' => [
                'total' => $viajesHoyTotal,
                'ingresos' => (float) $viajesHoyIngresos,
            ],
            'viajesPorHora' => $viajesPorHora,
            'viajesPorMunicipio' => [],
            'taxistasEstado' => $taxistasEstado,
            'usuarios' => $usuarios,
            'topTaxistas' => $topTaxistas,
        ]);
    }

    public function usuarios(Request $request)
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $query = User::query();
        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function eliminarUsuario($id)
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'Usuario eliminado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
    }

    public function viajes(Request $request)
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $query = Viaje::with(['cliente', 'taxista.user'])->orderBy('fecha_solicitud', 'desc');
        if ($request->filled('estado')) {
            $query->where('estado', $request->query('estado'));
        }

        $viajes = $query->get()->map(function ($viaje) {
            $viaje->cliente_nombre = $viaje->cliente ? $viaje->cliente->nombre : 'N/A';
            $viaje->taxista_nombre = $viaje->taxista && $viaje->taxista->user ? $viaje->taxista->user->nombre : null;
            return $viaje;
        });

        return response()->json($viajes);
    }

    public function cancelarViaje($id)
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        try {
            $viaje = Viaje::findOrFail($id);

            if (in_array($viaje->estado, ['finalizado', 'cancelado'], true)) {
                return response()->json(['error' => 'El viaje no puede cancelarse'], 400);
            }

            $viaje->update([
                'estado' => 'cancelado',
                'comentario' => 'Cancelado por administrador',
            ]);

            return response()->json(['message' => 'Viaje cancelado']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Viaje no encontrado'], 404);
        }
    }

    public function taxistas()
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $taxistas = Taxista::with('user')->get()->map(function ($taxista) {
            return [
                'id' => $taxista->id,
                'user_id' => $taxista->user_id,
                'nombre' => $taxista->user ? $taxista->user->nombre : 'N/A',
                'email' => $taxista->user ? $taxista->user->email : 'N/A',
                'licencia' => $taxista->licencia,
                'municipio' => $taxista->municipio,
                'matricula' => $taxista->matricula,
                'estado' => $taxista->estado,
                'valoracion_media' => $taxista->valoracion_media,
            ];
        });

        return response()->json($taxistas);
    }

    public function ubicacionesTaxistas()
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $taxistas = Taxista::with('user')
            ->whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function ($taxista) {
                return [
                    'id' => $taxista->id,
                    'nombre' => $taxista->user ? $taxista->user->nombre : 'N/A',
                    'licencia' => $taxista->licencia,
                    'estado' => $taxista->estado,
                    'matricula' => $taxista->matricula,
                    'latitud' => $taxista->latitud,
                    'longitud' => $taxista->longitud,
                ];
            });

        return response()->json($taxistas);
    }

    public function crearTaxista(Request $request)
    {
        if ($deny = $this->ensureAdmin()) {
            return $deny;
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string',
            'password' => 'required|string|min:6',
            'licencia' => 'required|string|unique:taxistas,licencia',
            'municipio' => 'required|string',
            'matricula' => 'required|string',
            'modeloVehiculo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validacion fallida',
                'detalles' => $validator->errors(),
            ], 400);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'role' => 'taxista',
        ]);

        $taxista = Taxista::create([
            'user_id' => $user->id,
            'licencia' => $request->licencia,
            'municipio' => $request->municipio,
            'matricula' => $request->matricula,
            'modelo_vehiculo' => $request->modeloVehiculo,
            'estado' => 'libre',
        ]);

        return response()->json([
            'message' => 'Taxista creado',
            'taxista' => $taxista,
        ], 201);
    }
}
