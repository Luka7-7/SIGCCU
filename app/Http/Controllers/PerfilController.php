<?php

// Ubicación: app/Http/Controllers/PerfilController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    // Valores permitidos
    private const AREAS = [
        'artes'                                => 'Artes',
        'ciencias basicas e ingenierias'       => 'Ciencias Básicas e Ingenierías',
        'ciencias economico y administrativas' => 'Económico Administrativas',
        'ciencias biologicas y agropecuarias'  => 'Ciencias Biológicas y Agropecuarias',
        'ciencias de la salud'                 => 'Ciencias de la Salud',
        'ciencias sociales y humanidades'      => 'Ciencias Sociales y Humanidades',
    ];

    private const UNIDADES = [
        'Unidad Académica de Ciencias e Ingenierías',
    ];

    private const PROGRAMAS = [
        'Ingeniería en Computación',
    ];

    private const ROLES = [
        'administrador' => 'Administrador',
        'alumno'        => 'Estudiante',
        'docente'       => 'Docente',
        'externo'       => 'Externo',
    ];

    /**
     * Muestra el perfil con sus datos y un resumen de su actividad.
     */
    public function show(Request $request)
    {
        $usuario = $request->user();

        $inscripciones = DB::table('inscripciones_congresos as ic')
            ->join('congresos as c', 'c.id', '=', 'ic.congreso_id')
            ->where('ic.usuario_id', $usuario->id)
            ->orderByDesc('ic.fecha_hora_inscripcion')
            ->select('c.nombre', 'c.siglas', 'c.edicion', 'ic.aceptado', 'ic.rol_congreso', 'ic.fecha_hora_inscripcion')
            ->get();

        $propuestas = DB::table('propuestas as p')
            ->join('congresos as c', 'c.id', '=', 'p.congreso_id')
            ->where('p.usuario_id', $usuario->id)
            ->orderByDesc('p.id')
            ->select('p.id', 'p.resumen_estado', 'c.siglas', 'c.edicion')
            ->get();

        $totalConstancias = DB::table('constancias')
            ->where('usuario_id', $usuario->id)
            ->count();

        return view('user.settings', [
            'usuario'          => $usuario,
            'inscripciones'    => $inscripciones,
            'propuestas'       => $propuestas,
            'totalConstancias' => $totalConstancias,
            'areas'            => self::AREAS,
            'unidades'         => $this->conActual(self::UNIDADES, $usuario->unidad_academica),
            'programas'        => $this->conActual(self::PROGRAMAS, $usuario->nombre_carrera),
            'roles'            => self::ROLES,
        ]);
    }

    /**
     * Actualiza los datos personales y académicos.
     */
    public function update(Request $request)
    {
        $usuario = $request->user();
        $requiereIdentificador = in_array($usuario->rol, ['alumno', 'docente'], true);

        $reglasCorreo = [
            'required', 'email', 'max:100',
            Rule::unique('usuarios', 'correo_electronico')->ignore($usuario->id),
        ];
        if ($requiereIdentificador) {
            $reglasCorreo[] = 'ends_with:@uan.edu.mx';
        }

        $reglasIdentificador = $requiereIdentificador
            ? ['required', 'string', 'max:20']
            : ['nullable'];

        // Sincronizamos 'matricula' (nombre en el HTML) con las reglas
        $data = $request->validate([
            'nombres'          => ['required', 'string', 'max:70'],
            'apellidos'        => ['required', 'string', 'max:80'],
            'email'            => $reglasCorreo,
            'matricula'        => $reglasIdentificador,
            'area'             => ['required', Rule::in(array_keys(self::AREAS))],
            'unidad_academica' => ['required', Rule::in($this->conActual(self::UNIDADES, $usuario->unidad_academica))],
            'nombre_carrera'   => ['required', Rule::in($this->conActual(self::PROGRAMAS, $usuario->nombre_carrera))],
        ], [
            'email.unique'       => 'Este correo ya está registrado.',
            'email.ends_with'    => 'Debes usar tu correo institucional (@uan.edu.mx).',
            'matricula.required' => $usuario->rol === 'docente'
                ? 'El número de empleado es obligatorio.'
                : 'La matrícula es obligatoria.',
        ]);

        $usuario->fill([
            'nombres'                 => $data['nombres'],
            'apellidos'               => $data['apellidos'],
            'correo_electronico'      => $data['email'],
            'matricula_numtrabajador' => $requiereIdentificador ? $data['matricula'] : null,
            'area'                    => $data['area'],
            'unidad_academica'        => $data['unidad_academica'],
            'nombre_carrera'          => $data['nombre_carrera'],
        ])->save();

        return redirect()
            ->route('profile.update')
            ->with('status', 'Tus datos se guardaron correctamente.');
    }

    /**
     * Cambia la contraseña.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed'                => 'Las contraseñas nuevas no coinciden.',
            'password.different'                => 'La contraseña nueva debe ser distinta de la actual.',
        ]);

        $usuario = $request->user();
        $usuario->contrasena = Hash::make($request->password);
        $usuario->save();

        $request->session()->regenerate();

        return redirect()
            ->route('settings')
            ->with('status', 'Tu contraseña se actualizó correctamente.');
    }

    /**
     * Agrega el valor actual a la lista si no está presente.
     */
    private function conActual(array $opciones, ?string $actual): array
    {
        if ($actual && ! in_array($actual, $opciones, true)) {
            $opciones[] = $actual;
        }

        return $opciones;
    }
}