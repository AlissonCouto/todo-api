<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

Trait HandlesApiException
{
    /**
     * Lida com exceções inesperadas de forma padronizada.
     *
     * @param  Throwable $e
     * @param  string $context
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleException(Throwable $e, string $context = '')
    {
        Log::error("Erro em $context", [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'ok' => false,
            'message' => 'Ocorreu um erro inesperado. Tente novamente mais tarde.',
        ], 500);
    }
}
