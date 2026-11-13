<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function redirectTo()
    {
        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar el rol del usuario y redirigir según el rol
        if ($user->hasRole('admin')) {
            return route('inmuebles.index'); // Reemplaza con la ruta a la vista del administrador
        } elseif ($user->hasRole('user')) {
            return '/ruta-usuario'; // Reemplaza con la ruta a la vista del usuario
        } else {
            // Puedes agregar lógica adicional o redirigir a una ruta predeterminada si el rol no coincide
            return '/ruta-sinrol';
        }
    }
}

