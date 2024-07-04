<?php

namespace App\Models\Bussiness;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Log;

class TokenGenerator extends Model
{
    use HasFactory;

    public function generateTokenFromCredential($credentials,$role){
        try{
            if(!$token['active_token'] = auth()->guard('api')->claims(['scope' => $role])->attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau Password Anda salah'
                ], 401);
            }
            $token['refresh_token'] = auth()->guard('api')->setTTL(config('jwt.refresh_ttl'))->attempt($credentials);
            return $token;
        }catch(Throwable $e){
            Log::error($e);
            return response()->json([
                'success' => false,
                'message' => 'Error on trying to generate token, please report'
            ], 401);
        }

    }

    public function generateTokenFromRefreshToken($token){

        

    }

    public function invalidateToken($token){
        try{
            $removeToken = JWTAuth::invalidate($token);
            if($removeToken){
                return true;
            }
        }catch(Throwable $e){
            Log::error($e);
            return false;
        }
    }

}
