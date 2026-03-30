<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class RestrictMobileApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $auth = $request->header('auth');
        if (!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى تسجيل الدخول',
                'data' => [],
            ], 203);
        }

        $user = User::where('remember_token', $auth)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'جلسة غير صالحة، يرجى إعادة تسجيل الدخول',
                'data' => [],
            ], 203);
        }

        if (!$user->can_access_mobile_app) {
            return response()->json([
                'status' => false,
                'message' => 'عذراً، ليس لديك صلاحية الوصول لتطبيق الموبايل',
                'data' => [],
            ], 203);
        }

        return $next($request);
    }
}
