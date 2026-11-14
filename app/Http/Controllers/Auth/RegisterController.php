<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Inmobiliaria;
use App\Models\Plan;
use App\Models\User;
use App\Rules\RecaptchaRule;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'nombre_comercial' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'g-recaptcha-response' => ['nullable', new RecaptchaRule],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telefono' => $data['telefono'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles(['inmobiliaria']);

        $planGratis = Plan::where('slug', 'gratis')->where('activo', 1)->first();

        Inmobiliaria::create([
            'usuario_id' => $user->id,
            'plan_id' => $planGratis?->id,
            'nombre_comercial' => $data['nombre_comercial'],
            'slug' => Inmobiliaria::generarSlug($data['nombre_comercial']),
            'telefono' => $data['telefono'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email' => $data['email'],
            'estado' => 'pendiente',
        ]);

        return $user;
    }
}
