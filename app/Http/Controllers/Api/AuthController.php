<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Services\AuthService;
use App\Traits\HandlesApiException;
use Throwable;

class AuthController extends Controller
{
    use HandlesApiException;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(StoreUserRequest $request)
    {
        try {
            $result = $this->authService->register($request->validated());
            return response()->json($result, 201);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function login(LoginUserRequest $request)
    {
        try {
            $result = $this->authService->login($request->email, $request->password);
            return response()->json($result, $result['ok'] ? 200 : 401);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function logout(Request $request)
    {
        try {
            $result = $this->authService->logout($request);
            return response()->json($result, $result['ok'] ? 200 : ($result['message'] === 'Token não encontrado' ? 400 : 401));
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }
}
