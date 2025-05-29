<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthService
{
    public function register(array $data): array
    {
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('api-token', ['post:read', 'post:create'])->plainTextToken;

        return [
            'ok' => true,
            'message' => 'Usuário cadastrado com sucesso',
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return [
                'ok' => false,
                'message' => 'Credenciais inválidas'
            ];
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'ok' => true,
            'token' => $token
        ];
    }

    public function logout(Request $request): array
    {
        $token = $request->bearerToken();

        if (!$token) {
            return [
                'ok' => false,
                'message' => 'Token não encontrado'
            ];
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return [
                'ok' => false,
                'message' => 'Token inválido'
            ];
        }

        if ($accessToken->tokenable_id !== $request->user()->id) {
            return [
                'ok' => false,
                'message' => 'Token não pertence ao usuário autenticado'
            ];
        }

        $accessToken->delete();

        return [
            'ok' => true,
            'message' => 'Desconectado com sucesso'
        ];
    }
}
