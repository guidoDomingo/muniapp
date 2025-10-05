<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // Mostrar el formulario de registro
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Registrar un nuevo usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::attempt($request->only('email', 'password'));

        return redirect('/');
    }

    // Mostrar el formulario de inicio de sesión
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Iniciar sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Redirigir según el rol del usuario
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('commission')) {
                return redirect()->route('commission.dashboard');
            } else {
                return redirect()->route('tramites.index');
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    // Cerrar sesión
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // Login específico para administradores
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Verificar si el usuario tiene rol de admin o commission
            if ($user->roles->whereIn('name', ['admin', 'commission'])->count() > 0) {
                if ($user->roles->where('name', 'admin')->count() > 0) {
                    return redirect()->route('admin.dashboard');
                } else {
                    return redirect()->route('commission.dashboard');
                }
            } else {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'No tienes permisos para acceder al panel de administración.',
                ]);
            }
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }
}

