<?php

namespace App\Http\Controllers;

use App\Models\RegistroAseo;
use App\Models\TurnoAseo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AseoController extends Controller
{
    /**
     * Actividades de aseo del domingo.
     *  - banos:   si todos llegan a tiempo, lo hace quien tiene el turno. Si alguien
     *             llega tarde, esa persona lo hace pero NO consume turno.
     *  - canecas: rotación estrictamente secuencial. Da igual llegar tarde o temprano;
     *             solo si alguien no puede venir, lo hace el siguiente de la lista y
     *             al que se saltó le vuelve a tocar el fin de semana siguiente.
     */
    private const ACTIVIDADES = [
        'banos' => [
            'nombre'       => 'Aseo de baños',
            'nombre_corto' => 'Baños',
            'icono'        => '🧹',
            'usa_motivo'   => true,
        ],
        'canecas' => [
            'nombre'       => 'Aseo de canecas',
            'nombre_corto' => 'Canecas',
            'icono'        => '🗑️',
            'usa_motivo'   => false,
        ],
    ];

    public function index(string $actividad)
    {
        $actividad = $this->validarActividad($actividad);
        $this->sincronizarRoster($actividad);

        $roster = $this->roster($actividad);
        $estado = $this->estadoRotacion($actividad, $roster);

        $registros = RegistroAseo::where('actividad', $actividad)
            ->with('registrador')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(150)
            ->get();

        return view('aseo.index', array_merge($estado, [
            'actividad'   => $actividad,
            'cfg'         => self::ACTIVIDADES[$actividad],
            'actividades' => self::ACTIVIDADES,
            'roster'      => $roster,
            'registros'   => $registros,
        ]));
    }

    public function store(Request $request, string $actividad)
    {
        $actividad = $this->validarActividad($actividad);
        $usaMotivo = self::ACTIVIDADES[$actividad]['usa_motivo'];

        $rules = [
            'user_id' => 'required|exists:users,id',
            'fecha'   => 'required|date|before_or_equal:today',
            'nota'    => 'nullable|string|max:255',
        ];
        if ($usaMotivo) {
            $rules['motivo'] = 'required|in:turno,llegada_tarde';
        }

        $request->validate($rules, [
            'user_id.required'      => 'Selecciona a la persona que realizó la actividad.',
            'user_id.exists'        => 'La persona seleccionada no existe.',
            'fecha.required'        => 'Indica la fecha en que se realizó.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'nota.max'              => 'La nota es demasiado larga.',
            'motivo.required'       => 'Indica el motivo.',
            'motivo.in'             => 'Motivo no válido.',
        ]);

        $motivo  = $usaMotivo ? $request->motivo : RegistroAseo::MOTIVO_TURNO;
        $usuario = User::findOrFail($request->user_id);

        $this->sincronizarRoster($actividad);
        $estado      = $this->estadoRotacion($actividad, $this->roster($actividad));
        $cicloActual = $estado['cicloActual'];

        if ($motivo === RegistroAseo::MOTIVO_TURNO) {
            $yaHizoTurno = RegistroAseo::where('actividad', $actividad)
                ->where('motivo', RegistroAseo::MOTIVO_TURNO)
                ->where('ciclo', $cicloActual)
                ->where('user_id', $usuario->id)
                ->exists();

            if ($yaHizoTurno) {
                return back()->with('error', $usuario->name . ' ya realizó su turno en el ciclo actual.');
            }
        }

        RegistroAseo::create([
            'actividad'      => $actividad,
            'user_id'        => $usuario->id,
            'nombre_usuario' => $usuario->name,
            'fecha'          => $request->fecha,
            'motivo'         => $motivo,
            'nota'           => $request->nota,
            'ciclo'          => $cicloActual,
            'registrado_por' => auth()->id(),
        ]);

        $msg = $motivo === RegistroAseo::MOTIVO_TURNO
            ? 'Registrado. Se marcó el turno de ' . $usuario->name . '.'
            : 'Registrado por llegada tarde de ' . $usuario->name . '.';

        return redirect()->route('aseo.index', $actividad)->with('success', $msg);
    }

    public function destroy(string $actividad, $id)
    {
        $actividad = $this->validarActividad($actividad);

        $registro = RegistroAseo::where('actividad', $actividad)->findOrFail($id);
        $nombre = $registro->nombre_usuario;
        $registro->delete();

        return redirect()->route('aseo.index', $actividad)
            ->with('success', 'Registro de ' . $nombre . ' eliminado.');
    }

    public function subir(string $actividad, $id)
    {
        $actividad = $this->validarActividad($actividad);
        $this->intercambiarOrden($actividad, $id, 'subir');

        return redirect()->route('aseo.index', $actividad);
    }

    public function bajar(string $actividad, $id)
    {
        $actividad = $this->validarActividad($actividad);
        $this->intercambiarOrden($actividad, $id, 'bajar');

        return redirect()->route('aseo.index', $actividad);
    }

    public function toggle(string $actividad, $id)
    {
        $actividad = $this->validarActividad($actividad);

        $turno = TurnoAseo::where('actividad', $actividad)->findOrFail($id);
        $turno->activo = ! $turno->activo;
        $turno->save();

        $estado = $turno->activo ? 'incluida en' : 'retirada de';
        return redirect()->route('aseo.index', $actividad)
            ->with('success', ($turno->user->name ?? 'Persona') . ' fue ' . $estado . ' la rotación.');
    }

    /* ─────────────────────────────────────────────────────────── */

    private function validarActividad(string $actividad): string
    {
        abort_unless(isset(self::ACTIVIDADES[$actividad]), 404);
        return $actividad;
    }

    /**
     * @return \Illuminate\Support\Collection<int,\App\Models\TurnoAseo>
     */
    private function roster(string $actividad)
    {
        return TurnoAseo::where('actividad', $actividad)
            ->with('user')
            ->orderBy('orden')
            ->orderBy('id')
            ->get()
            ->filter(fn ($t) => $t->user !== null)
            ->values();
    }

    /**
     * Crea una fila en turnos_aseo para cada usuario que aún no esté en la lista
     * de esta actividad.
     */
    private function sincronizarRoster(string $actividad): void
    {
        $conTurno  = TurnoAseo::where('actividad', $actividad)->pluck('user_id')->all();
        $faltantes = User::whereNotIn('id', $conTurno)->orderBy('name')->get();

        if ($faltantes->isEmpty()) {
            return;
        }

        $orden = (int) TurnoAseo::where('actividad', $actividad)->max('orden');

        foreach ($faltantes as $usuario) {
            $orden++;
            TurnoAseo::create([
                'actividad' => $actividad,
                'user_id'   => $usuario->id,
                'orden'     => $orden,
                // Los empleados entran a la rotación; los administradores quedan
                // fuera por defecto y se agregan con "Incluir" si también trabajan.
                'activo'    => ! $usuario->esAdmin(),
            ]);
        }
    }

    /**
     * Estado de la rotación: ciclo actual, quién ya cumplió su turno y a quién le toca.
     *
     * En ambas actividades el turno es "el primero de la lista (por orden) que aún no
     * ha cumplido en el ciclo actual". Para canecas eso ya implementa la regla de que,
     * si alguien no vino y lo hizo el siguiente, a la persona saltada le vuelve a tocar.
     *
     * @param  \Illuminate\Support\Collection<int,\App\Models\TurnoAseo>  $roster
     */
    private function estadoRotacion(string $actividad, $roster): array
    {
        $activos = $roster->where('activo')->values();

        $base = fn () => RegistroAseo::where('actividad', $actividad)
            ->where('motivo', RegistroAseo::MOTIVO_TURNO);

        $maxCiclo = (int) ($base()->max('ciclo') ?? 1);

        $hechosEnCiclo = $base()->where('ciclo', $maxCiclo)->pluck('user_id')->all();

        $pendientes = $activos->reject(fn ($t) => in_array($t->user_id, $hechosEnCiclo, true))->values();

        $cicloCompletado = false;

        if ($pendientes->isEmpty() && $activos->isNotEmpty()) {
            // Todos cumplieron el ciclo → arranca uno nuevo
            $cicloCompletado = true;
            $cicloActual     = $maxCiclo + 1;
            $hechosEnCiclo    = [];
            $pendientes       = $activos;
        } else {
            $cicloActual = $maxCiclo;
        }

        $turnoActual = $pendientes->sortBy('orden')->first();

        $fechasCiclo = $base()->where('ciclo', $cicloActual)
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

    private function intercambiarOrden(string $actividad, $id, string $dir): void
    {
        $actual = TurnoAseo::where('actividad', $actividad)->findOrFail($id);

        $query = TurnoAseo::where('actividad', $actividad);
        if ($dir === 'subir') {
            $vecino = $query->where('orden', '<', $actual->orden)->orderByDesc('orden')->first();
        } else {
            $vecino = $query->where('orden', '>', $actual->orden)->orderBy('orden')->first();
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
