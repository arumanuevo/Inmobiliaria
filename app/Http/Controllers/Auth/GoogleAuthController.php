<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Inmobiliaria;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        abort_if(!config('services.google.client_id'), 404);

        $state = Str::random(40);
        session(['google_oauth_state' => $state]);

        $redirectUri = config('services.google.redirect') ?: url('/auth/google/callback');

        $params = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    }

    public function callback(Request $request)
    {
        if ($request->input('state') !== session('google_oauth_state')) {
            abort(403, 'Estado OAuth inválido.');
        }
        session()->forget('google_oauth_state');

        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Se canceló el inicio de sesión con Google.');
        }

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $request->input('code'),
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect') ?: url('/auth/google/callback'),
            'grant_type' => 'authorization_code',
        ]);

        if (!$tokenResponse->ok()) {
            return redirect()->route('login')->with('error', 'No se pudo obtener el token de Google.');
        }

        $userInfo = Http::withToken($tokenResponse->json('access_token'))
            ->get('https://openidconnect.googleapis.com/v1/userinfo');

        $email = $userInfo->json('email');
        if (!$userInfo->ok() || !$email) {
            return redirect()->route('login')->with('error', 'No se pudo obtener tu email de Google.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $nombre = $userInfo->json('name') ?: $email;

            $user = User::create([
                'name' => $nombre,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(32)),
            ]);
            $user->syncRoles(['inmobiliaria']);

            $planGratis = Plan::where('slug', 'gratis')->where('activo', 1)->first();

            Inmobiliaria::create([
                'usuario_id' => $user->id,
                'plan_id' => $planGratis?->id,
                'nombre_comercial' => $nombre,
                'slug' => Inmobiliaria::generarSlug($nombre),
                'email' => $email,
                'estado' => 'pendiente',
            ]);

            Auth::login($user);

            return redirect()->route('inmobiliaria.perfil')
                ->with('success', 'Cuenta creada con Google. Completá los datos de tu inmobiliaria; quedará pendiente de aprobación.');
        }

        Auth::login($user);

        return redirect()->intended(route('home'));
    }
}
