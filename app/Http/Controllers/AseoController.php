<?php

namespace App\Http\Controllers;

use App\Models\RegistroAseo;
use App\Models\TurnoAseo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AseoController extends Controller
{
    public function index()
    {
        $this->sincronizarRoster();

        $roster = TurnoAseo::with('user')
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->filter(fn ($t) => $t->user !== null)
            ->values();

        $estado = $this->estadoRotacion($roster);

        $registros = RegistroAseo::with('registrador')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(150)
            ->get();

        return view('aseo.index', array_merge($estado, [
            'roster'    => $roster,
            'registros' => $registros,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'fecha'   => 'required|date|before_or_equal:today',
            'motivo'  => 'required|in:turno,llegada_tarde',
        ], [
            'user_id.required' => 'Selecciona a la persona que realizó el aseo.',
            'user_id.exists'   => 'La persona seleccionada no existe.',
            'fecha.required'   => 'Indica la fecha en que se realizó el aseo.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'motivo.required'  => 'Indica el motivo.',
            'motivo.in'        => 'Motivo no válido.',
        ]);

        $usuario = User::findOrFail($request->user_id);

        $this->sincronizarRoster();
        $roster = TurnoAseo::with('user')
            ->orderBy('orden')->orderBy('id')->get()
            ->filter(fn ($t) => $t->user !== null)->values();
        $estado = $this->estadoRotacion($roster);
        $cicloActual = $estado['cicloActual'];

        if ($request->motivo === RegistroAseo::MOTIVO_TURNO) {
            $yaHizoTurno = RegistroAseo::where('motivo', RegistroAseo::MOTIVO_TURNO)
                ->where('ciclo', $cicloActual)
                ->where('user_id', $usuario->id)
                ->exists();

            if ($yaHizoTurno) {
                return back()->with('error', $usuario->name . ' ya realizó su turno en el ciclo actual.');
            }
        }

        RegistroAseo::create([
            'user_id'        => $usuario->id,
            'nombre_usuario' => $usuario->name,
            'fecha'          => $request->fecha,
            'motivo'         => $request->motivo,
            'ciclo'          => $cicloActual,
            'registrado_por' => auth()->id(),
        ]);

        $msg = $request->motivo === RegistroAseo::MOTIVO_TURNO
            ? 'Aseo registrado. Se marcó el turno de ' . $usuario->name . '.'
            : 'Aseo registrado por llegada tarde de ' . $usuario->name . '.';

        return redirect()->route('aseo.index')->with('success', $msg);
    }

    public function destroy($id)
    {
        $registro = RegistroAseo::findOrFail($id);
        $nombre = $registro->nombre_usuario;
        $registro->delete();

        return redirect()->route('aseo.index')
            ->with('success', 'Registro de aseo de ' . $nombre . ' eliminado.');
    }

    public function subir($id)
    {
        $this->intercambiarOrden($id, 'subir');
        return redirect()->route('aseo.index');
    }

    public function bajar($id)
    {
        $this->intercambiarOrden($id, 'bajar');
        return redirect()->route('aseo.index');
    }

    public function toggle($id)
    {
        $turno = TurnoAseo::findOrFail($id);
        $turno->activo = ! $turno->activo;
        $turno->save();

        $estado = $turno->activo ? 'incluida en' : 'retirada de';
        return redirect()->route('aseo.index')
            ->with('success', ($turno->user->name ?? 'Persona') . ' fue ' . $estado . ' la rotación de aseo.');
    }

    /* ─────────────────────────────────────────────────────────── */

    /**
     * Crea una fila en turnos_aseo para cada usuario que aún no la tenga.
     */
    private function sincronizarRoster(): void
    {
        $conTurno = TurnoAseo::pluck('user_id')->all();
        $faltantes = User::whereNotIn('id', $conTurno)->orderBy('name')->get();

        if ($faltantes->isEmpty()) {
            return;
        }

        $orden = (int) TurnoAseo::max('orden');

        foreach ($faltantes as $usuario) {
            $orden++;
            TurnoAseo::create([
                'user_id' => $usuario->id,
                'orden'   => $orden,
                // Los empleados entran a la rotación; los administradores
                // quedan fuera por defecto y se agregan con "Incluir" si también trabajan.
                'activo'  => ! $usuario->esAdmin(),
            ]);
        }
    }

    /**
     * Devuelve el estado de la rotación: ciclo actual, quién ya hizo su turno
     * y a quién le toca.
     *
     * @param  \Illuminate\Support\Collection<int,\App\Models\TurnoAseo>  $roster
     */
    private function estadoRotacion($roster): array
    {
        $activos = $roster->where('activo')->values();

        $maxCiclo = (int) (RegistroAseo::where('motivo', RegistroAseo::MOTIVO_TURNO)->max('ciclo') ?? 1);

        $hechosEnCiclo = RegistroAseo::where('motivo', RegistroAseo::MOTIVO_TURNO)
            ->where('ciclo', $maxCiclo)
            ->pluck('user_id')
            ->all();

        $pendientes = $activos->reject(fn ($t) => in_array($t->user_id, $hechosEnCiclo, true))->values();

        $cicloCompletado = false;

        if ($pendientes->isEmpty() && $activos->isNotEmpty()) {
            // Todos completaron el ciclo → arranca uno nuevo
            $cicloCompletado = true;
            $cicloActual = $maxCiclo + 1;
            $hechosEnCiclo = [];
            $pendientes = $activos;
        } else {
            $cicloActual = $maxCiclo;
        }

        $turnoActual = $pendientes->sortBy('orden')->first();

        // Fecha del último turno de cada persona en el ciclo mostrado
        $fechasCiclo = RegistroAseo::where('motivo', RegistroAseo::MOTIVO_TURNO)
            ->where('ciclo', $cicloActual)
            ->orderBy('fecha')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($grupo) => $grupo->last()->fecha);

        return [
            'cicloActual'     => $cicloActual,
            'cicloCompletado' => $cicloCompletado,
            'hechosEnCiclo'   => $hechosEnCiclo,
            'turnoActual'     => $turnoActual,
            'fechasCiclo'     => $fechasCiclo,
            'totalActivos'    => $activos->count(),
        ];
    }

    private function intercambiarOrden($id, string $dir): void
    {
        $actual = TurnoAseo::findOrFail($id);

        $vecinoQuery = TurnoAseo::query();
        if ($dir === 'subir') {
            $vecino = $vecinoQuery->where('orden', '<', $actual->orden)
                ->orderByDesc('orden')->first();
        } else {
            $vecino = $vecinoQuery->where('orden', '>', $actual->orden)
                ->orderBy('orden')->first();
        }

        if (! $vecino) {
            return;
        }

        DB::transaction(function () use ($actual, $vecino) {
            [$actual->orden, $vecino->orden] = [$vecino->orden, $actual->orden];
            $actual->save();
            $vecino->save();
        });
    }
}
