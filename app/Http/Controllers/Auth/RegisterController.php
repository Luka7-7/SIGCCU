<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // Equivalencias formulario -> base de datos
    private const ROLES = [
        'estudiante' => 'alumno',
        'docente'    => 'docente',
        'externo'    => 'externo',
    ];

    private const AREAS = [
        'CB' => 'ciencias basicas e ingenierias',
        'CS' => 'ciencias de la salud',
        'EA' => 'ciencias economico y administrativas',
        'SH' => 'ciencias sociales y humanidades',
    ];

    private const UNIDADES = [
        '02' => 'Unidad Académica de Ciencias e Ingenierías',
    ];

    private const PROGRAMAS = [
        '03' => 'Ingeniería en Computación',
    ];

    public function store(Request $request)
    {
        $data = $request->validate([
            'perfil_inicial'     => ['required', Rule::in(array_keys(self::ROLES))],
            'nombres'            => ['required', 'string', 'max:70'],
            'apellidos'          => ['required', 'string', 'max:80'],
            'email'              => ['required', 'email', 'max:100', 'unique:usuarios,correo_electronico'],
            'password'           => ['required', 'string', 'min:8', 'confirmed'],
            'matricula'          => ['nullable', 'required_if:perfil_inicial,estudiante', 'string', 'max:8'],
            'numero_empleado'    => ['nullable', 'required_if:perfil_inicial,docente', 'string', 'max:8'],
            'area_academica'     => ['required', Rule::in(array_keys(self::AREAS))],
            'unidad_academica'   => ['required', Rule::in(array_keys(self::UNIDADES))],
            'programa_academico' => ['required', Rule::in(array_keys(self::PROGRAMAS))],
        ], [
            'email.unique'                   => 'Este correo ya está registrado.',
            'password.confirmed'             => 'Las contraseñas no coinciden.',
            'matricula.required_if'          => 'La matrícula es obligatoria para estudiantes.',
            'numero_empleado.required_if'    => 'El número de empleado es obligatorio para docentes.',
        ]);

        // Matrícula o número de empleado según el perfil
        $identificador = match ($data['perfil_inicial']) {
            'estudiante' => $data['matricula'] ?? null,
            'docente'    => $data['numero_empleado'] ?? null,
            default      => null,
        };

        $usuario = Usuario::create([
            'correo_electronico'      => $data['email'],
            'contrasena'              => Hash::make($data['password']),
            'nombres'                 => $data['nombres'],
            'apellidos'               => $data['apellidos'],
            'rol'                     => self::ROLES[$data['perfil_inicial']],
            'institucion'             => 'Universidad Autónoma de Nayarit',
            'matricula_numtrabajador' => $identificador,
            'unidad_academica'        => self::UNIDADES[$data['unidad_academica']],
            'nombre_carrera'          => self::PROGRAMAS[$data['programa_academico']],
            'area'                    => self::AREAS[$data['area_academica']],
        ]);

        Auth::login($usuario);

        return redirect('/usuario.perfil'); // ajusta a tu ruta
    }
}