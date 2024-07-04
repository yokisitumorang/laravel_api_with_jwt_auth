<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Log;
class RoleAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            //Access token from the request        
            $token = JWTAuth::parseToken();
            $payload = JWTAuth::parseToken()->getPayload();
            
        // Log::debug($token);

            //Try authenticating user       
            $user = $token->authenticate();
            $scope = $payload['scope'];


        } catch (TokenExpiredException $e) {
            //Thrown if token has expired        
            return $this->unauthorized('Your token has expired. Please, login again.');
        } catch (TokenInvalidException $e) {
            //Thrown if token invalid
            return $this->unauthorized('Your token is invalid. Please, login again.');
        }catch (JWTException $e) {
            //Thrown if token was not found in the request.
            return $this->unauthorized('Please, attach a Bearer Token to your request');
        }
        //If user was authenticated successfully and user is in one of the acceptable roles, send to next request.
        Log::debug($roles);
        if ($user && in_array($scope, $roles)) {
            return $next($request);
        }
    
        return $this->unauthorized();
    }

    private function unauthorized($message = null){
        return response()->json([
            'success' => false,
            'message' => $message ? $message : 'You are unauthorized to access this resource'
        ], 401);
    }
}
