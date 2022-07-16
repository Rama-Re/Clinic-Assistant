<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
class checkVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $result = $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ]);
        $user = User::where('phone_number',$result['phone_number'])->first();
        
        if ($user->is_verified) {
            return $next($request);
        }
        $response = [
            'message' => 'account is not verified'
        ];
        return response($response,401);
    }
}
