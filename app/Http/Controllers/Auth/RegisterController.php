<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    // Equivalencias formulario -> base de datos
    private const ROLES = [
        'estudiante' => 'alumno',
        'docente'    => 'docente',
        'externo'    => 'externo',
    ];

    public function create(): View
    {
        return view('auth.register', [
            'carreras' => Carrera::query()
                ->select('area', 'unidad_academica', 'nombre_carrera')
                ->orderBy('area')
                ->orderBy('unidad_academica')
                ->orderBy('nombre_carrera')
                ->distinct()
                ->get(),
        ]);
    }

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
            'area_academica'     => ['required', 'string', Rule::exists('Carrera', 'area')],
            'unidad_academica'   => ['required', 'string', Rule::exists('Carrera', 'unidad_academica')],
            'programa_academico' => ['required', 'string', Rule::exists('Carrera', 'nombre_carrera')],
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

        $carrera = Carrera::query()
            ->where('area', $data['area_academica'])
            ->where('unidad_academica', $data['unidad_academica'])
            ->where('nombre_carrera', $data['programa_academico'])
            ->first();

        if (! $carrera) {
            throw ValidationException::withMessages([
                'programa_academico' => 'La carrera seleccionada no está disponible.',
            ]);
        }

        $usuario = Usuario::create([
            'correo_electronico'      => $data['email'],
            'contrasena'              => Hash::make($data['password']),
            'nombres'                 => $data['nombres'],
            'apellidos'               => $data['apellidos'],
            'rol'                     => self::ROLES[$data['perfil_inicial']],
            'matricula_numtrabajador' => $identificador,
            'Carrera_id'              => $carrera->id,
        ]);

        Auth::login($usuario);

        return redirect()->route('profile');
    }
}