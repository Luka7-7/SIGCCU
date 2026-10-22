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

    // Catálogo de Unidades Académicas y sus Programas Académicos (Carreras)
    public const UNIDADES_PROGRAMAS = [
        'Unidad Académica de Ahuacatlán' => [
            'Licenciatura en Contaduría',
            'Licenciatura en Administración',
            'Licenciatura en Enfermería',
        ],
        'Unidad Académica de Bahía de Banderas' => [
            'Licenciatura en Administración',
            'Licenciatura en Contaduría',
            'Licenciatura en Ciencias de la Educación',
            'Licenciatura en Derecho',
            'Licenciatura en Psicología',
        ],
        'Unidad Académica de Ixtlán' => [
            'Licenciatura en Administración',
            'Licenciatura en Ciencias de la Educación',
            'Licenciatura en Contaduría',
            'Licenciatura en Derecho',
        ],
        'Unidad Académica de Agricultura' => [
            'Licenciatura en Agricultura',
            'Licenciatura en Biología',
        ],
        'Unidad Académica de Artes' => [
            'Licenciatura en Música',
            'Licenciatura en Diseño Urbano y Edificación',
        ],
        'Unidad Académica de Ciencias Básicas e Ingenierías' => [
            'Ingeniería en Automatización y Sistemas Inteligentes',
            'Ingeniería en Electrónica',
            'Ingeniería en Mecánica',
            'Ingeniería Química',
            'Licenciatura en Matemáticas',
        ],
        'Unidad Académica de Ciencias Sociales' => [
            'Licenciatura en Psicología',
            'Licenciatura en Comunicación y Medios',
            'Licenciatura en Ciencias Políticas y Gobierno',
            'Licenciatura en Estudios Coreanos',
        ],
        'Unidad Académica de Ciencias Químico, Biológicas y Farmacéuticas' => [
            'Licenciatura en Químico Farmacobiólogo',
        ],
        'Unidad Académica de Contaduría y Administración' => [
            'Licenciatura en Contaduría',
            'Licenciatura en Administración',
            'Licenciatura en Mercadotecnia',
            'Licenciatura en Negocios Internacionales',
            'Licenciatura en Administración Pública',
        ],
        'Unidad Académica de Derecho' => [
            'Licenciatura en Derecho',
        ],
        'Unidad Académica de Economía' => [
            'Licenciatura en Economía',
            'Licenciatura en Seguridad Informática',
            'Licenciatura en Sistemas Computacionales',
            'Licenciatura en Proyectos de Tecnologías de Información e Innovación Digital',
        ],
        'Unidad Académica de Educación y Humanidades' => [
            'Licenciatura en Ciencias de la Educación',
            'Licenciatura en Filosofía',
            'Licenciatura en Lingüística Aplicada',
            'Licenciatura en Educación Infantil',
            'Licenciatura en Educación Inicial y Gestión de Instituciones',
            'Licenciatura en Estudios y Creación Literaria',
        ],
        'Unidad Académica de Enfermería' => [
            'Licenciatura en Enfermería',
        ],
        'Unidad Académica de Ingeniería Pesquera' => [
            'Ingeniería Pesquera',
            'Ingeniería en Acuicultura',
        ],
        'Unidad Académica de Medicina' => [
            'Licenciatura en Médico Cirujano',
        ],
        'Unidad Académica de Medicina Veterinaria y Zootecnia' => [
            'Licenciatura en Medicina Veterinaria y Zootecnia',
        ],
        'Unidad Académica de Turismo' => [
            'Licenciatura en Gastronomía',
            'Licenciatura en Gestión e Innovación del Turismo Escolarizado',
            'Licenciatura en Gestión e Innovación del Turismo Semiescolarizado',
        ],
        'Unidad Académica de Odontología' => [
            'Licenciatura en Cirujano Dentista',
        ],
        'Unidad Académica de Salud Integral' => [
            'Licenciatura en Nutrición',
            'Licenciatura en Cultura Física y Deporte',
            'Licenciatura en Terapia Física',
        ],
        'Centro Especializado de Educación Virtual' => [
            'Licenciatura en Biomedicina Ambiental Traslacional Escolarizado',
            'Licenciatura en Administración Pública en línea',
            'Licenciatura en Gestión Empresarial en línea',
            'Licenciatura en Derecho en línea',
            'Licenciatura en Contaduría en línea',
            'Licenciatura en Administración en línea',
            'Licenciatura en Creación de Negocios en línea',
        ],
        'Unidad Académica de Ciencias e Ingenierías' => [
            'Ingeniería en Computación',
        ],
    ];

    public const UNIDADES = [
        'Unidad Académica de Ahuacatlán',
        'Unidad Académica de Bahía de Banderas',
        'Unidad Académica de Ixtlán',
        'Unidad Académica de Agricultura',
        'Unidad Académica de Artes',
        'Unidad Académica de Ciencias Básicas e Ingenierías',
        'Unidad Académica de Ciencias Sociales',
        'Unidad Académica de Ciencias Químico, Biológicas y Farmacéuticas',
        'Unidad Académica de Contaduría y Administración',
        'Unidad Académica de Derecho',
        'Unidad Académica de Economía',
        'Unidad Académica de Educación y Humanidades',
        'Unidad Académica de Enfermería',
        'Unidad Académica de Ingeniería Pesquera',
        'Unidad Académica de Medicina',
        'Unidad Académica de Medicina Veterinaria y Zootecnia',
        'Unidad Académica de Turismo',
        'Unidad Académica de Odontología',
        'Unidad Académica de Salud Integral',
        'Centro Especializado de Educación Virtual',
        'Unidad Académica de Ciencias e Ingenierías',
    ];

    public const PROGRAMAS = [
        'Ingeniería en Automatización y Sistemas Inteligentes',
        'Ingeniería en Computación',
        'Ingeniería en Electrónica',
        'Ingeniería en Mecánica',
        'Ingeniería Pesquera',
        'Ingeniería Química',
        'Ingeniería en Acuicultura',
        'Licenciatura en Administración',
        'Licenciatura en Administración Pública',
        'Licenciatura en Administración Pública en línea',
        'Licenciatura en Administración en línea',
        'Licenciatura en Agricultura',
        'Licenciatura en Biología',
        'Licenciatura en Biomedicina Ambiental Traslacional Escolarizado',
        'Licenciatura en Ciencias Políticas y Gobierno',
        'Licenciatura en Ciencias de la Educación',
        'Licenciatura en Cirujano Dentista',
        'Licenciatura en Comunicación y Medios',
        'Licenciatura en Contaduría',
        'Licenciatura en Contaduría en línea',
        'Licenciatura en Creación de Negocios en línea',
        'Licenciatura en Cultura Física y Deporte',
        'Licenciatura en Derecho',
        'Licenciatura en Derecho en línea',
        'Licenciatura en Diseño Urbano y Edificación',
        'Licenciatura en Economía',
        'Licenciatura en Educación Infantil',
        'Licenciatura en Educación Inicial y Gestión de Instituciones',
        'Licenciatura en Enfermería',
        'Licenciatura en Estudios Coreanos',
        'Licenciatura en Estudios y Creación Literaria',
        'Licenciatura en Filosofía',
        'Licenciatura en Gastronomía',
        'Licenciatura en Gestión Empresarial en línea',
        'Licenciatura en Gestión e Innovación del Turismo Escolarizado',
        'Licenciatura en Gestión e Innovación del Turismo Semiescolarizado',
        'Licenciatura en Lingüística Aplicada',
        'Licenciatura en Matemáticas',
        'Licenciatura en Medicina Veterinaria y Zootecnia',
        'Licenciatura en Mercadotecnia',
        'Licenciatura en Música',
        'Licenciatura en Médico Cirujano',
        'Licenciatura en Negocios Internacionales',
        'Licenciatura en Nutrición',
        'Licenciatura en Proyectos de Tecnologías de Información e Innovación Digital',
        'Licenciatura en Psicología',
        'Licenciatura en Químico Farmacobiólogo',
        'Licenciatura en Seguridad Informática',
        'Licenciatura en Sistemas Computacionales',
        'Licenciatura en Terapia Física',
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

        return view('user.settings.personal', [
            'usuario'          => $usuario,
            'inscripciones'    => $inscripciones,
            'propuestas'       => $propuestas,
            'totalConstancias' => $totalConstancias,
            'areas'            => self::AREAS,
            'unidades'         => $this->conActual(self::UNIDADES, $usuario->unidad_academica),
            'programas'        => $this->conActual(self::PROGRAMAS, $usuario->nombre_carrera),
            'unidadesProgramas'=> self::UNIDADES_PROGRAMAS,
            'roles'            => self::ROLES,
        ]);
    }

    /**
     * Muestra la sección de datos académicos.
     */
    public function showAcademic(Request $request)
    {
        $usuario = $request->user();

        return view('user.settings.academic', [
            'usuario'   => $usuario,
            'areas'     => self::AREAS,
            'unidades'  => $this->conActual(self::UNIDADES, $usuario->unidad_academica),
            'programas' => $this->conActual(self::PROGRAMAS, $usuario->nombre_carrera),
            'roles'     => self::ROLES,
        ]);
    }

    /**
     * Muestra la sección de ajustes generales.
     */
    public function showGeneral(Request $request)
    {
        $usuario = $request->user();

        return view('user.settings.general', [
            'usuario' => $usuario,
        ]);
    }

    /**
     * Actualiza únicamente los datos personales del usuario.
     */
    public function updatePersonal(Request $request)
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

        $data = $request->validate([
            'nombres'   => ['required', 'string', 'max:70'],
            'apellidos' => ['required', 'string', 'max:80'],
            'email'     => $reglasCorreo,
        ], [
            'email.unique'    => 'Este correo ya está registrado.',
            'email.ends_with' => 'Debes usar tu correo institucional (@uan.edu.mx).',
        ]);

        $usuario->fill([
            'nombres'            => $data['nombres'],
            'apellidos'          => $data['apellidos'],
            'correo_electronico' => $data['email'],
        ])->save();

        return redirect()
            ->route('settings')
            ->with('status', 'Tus datos personales se guardaron correctamente.');
    }

    /**
     * Actualiza únicamente los datos académicos del usuario.
     */
    public function updateAcademic(Request $request)
    {
        $usuario = $request->user();
        $requiereIdentificador = in_array($usuario->rol, ['alumno', 'docente'], true);

        $reglasIdentificador = $requiereIdentificador
            ? ['required', 'string', 'max:20']
            : ['nullable'];

        $unidadesValidas = array_unique(array_merge(
            self::UNIDADES,
            array_map('mb_strtoupper', self::UNIDADES)
        ));

        $data = $request->validate([
            'matricula'        => $reglasIdentificador,
            'area'             => ['required', Rule::in(array_keys(self::AREAS))],
            'unidad_academica' => ['required', Rule::in($this->conActual($unidadesValidas, $usuario->unidad_academica))],
            'nombre_carrera'   => ['required', Rule::in($this->conActual(self::PROGRAMAS, $usuario->nombre_carrera))],
        ], [
            'matricula.required' => $usuario->rol === 'docente'
                ? 'El número de empleado es obligatorio.'
                : 'La matrícula es obligatoria.',
        ]);

        // Si se envió en mayúsculas, se normaliza al nombre canónico
        $unidadNormalizada = $data['unidad_academica'];
        foreach (self::UNIDADES as $unidadCanonica) {
            if (mb_strtolower($unidadCanonica) === mb_strtolower($unidadNormalizada)) {
                $unidadNormalizada = $unidadCanonica;
                break;
            }
        }

        $usuario->fill([
            'matricula_numtrabajador' => $requiereIdentificador ? $data['matricula'] : null,
            'area'                    => $data['area'],
            'unidad_academica'        => $unidadNormalizada,
            'nombre_carrera'          => $data['nombre_carrera'],
        ])->save();

        return redirect()
            ->route('settings.academic')
            ->with('status', 'Tus datos académicos se guardaron correctamente.');
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
            ->route('settings.general')
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

    /**
     * Obtiene los programas académicos asociados a una unidad o todos si no se especifica.
     */
    public static function getProgramasPorUnidad(?string $unidad = null): array
    {
        if ($unidad) {
            foreach (self::UNIDADES_PROGRAMAS as $u => $carreras) {
                if (mb_strtolower($u) === mb_strtolower($unidad)) {
                    return $carreras;
                }
            }
        }

        return self::PROGRAMAS;
    }
}