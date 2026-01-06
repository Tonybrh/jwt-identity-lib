<?php

namespace Tonybrh\JwtIdentityLib\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtIdentityMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowedTypes): Response
    {
        $payload = JWTAuth::parseToken()->getPayload();

        $idClaim = config('jwt-identity.claims.id', 'sub');
        $typeClaim = config('jwt-identity.claims.type', 'identity_type');

        $id = $payload->get($idClaim);
        $type = $payload->get($typeClaim);

        $map = config('jwt-identity.identity_map');

        if (! $id || ! $type || ! isset($map[$type])) {
            return response()->json(['message' => 'Identidade inválida'], 401);
        }

        if (! empty($allowedTypes) && ! in_array($type, $allowedTypes, true)) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        $model = $map[$type];
        $identity = $model::find($id);

        if (! $identity) {
            return response()->json(['message' => 'Identidade não encontrada'], 401);
        }

        auth()->setUser($identity);

        $request->attributes->set('identity_type', $type);
        $request->attributes->set('jwt_payload', $payload);

        return $next($request);
    }
}
