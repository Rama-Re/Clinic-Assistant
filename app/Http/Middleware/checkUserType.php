<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkUserType
{
    public function handle(Request $request, Closure $next,...$roles)
    {
        $user = auth()->user();
        foreach ($roles as $value){
            if ($user->type == $value){
                return $next($request);
            }
        }
        
        $response = [
            'message' => 'you don\'t have premision to do that'
        ];
        return response($response,401);
        return $next($request);
    }
}
