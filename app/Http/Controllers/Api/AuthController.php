<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bussiness\TokenGenerator;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //get credentials from request
        $credentials = $request->only('username', 'password');
        
        


        // get user/client data and saving value
        $user = User::where('username', $request->username)->first();
        if($user->is_active == 0){
            return response()->json([
                'success' => false,
                'message' => 'Incorrect Credentials'
            ], 401);
        }

        $model = new TokenGenerator;
        $token = $model->generateTokenFromCredential($credentials,$user['role']);

        $user->active_token = $token['active_token'];
        $user->refresh_token = $token['refresh_token'];
        $user->save();

        //if auth success
        return response()->json([
            'status' => true,
            // 'user'    => auth()->guard('api')->user(),
            'token'   => $token['active_token'],
            'refresh_token'   => $token['refresh_token']
        ], 200);
    }

    public function logout(Request $request)
    {

        $model = new TokenGenerator;
        $invalidate = $model->invalidateToken(JWTAuth::getToken());
        if($invalidate){
            return response()->json([
                'success' => true,
                'message' => 'Logout Berhasil!',  
            ]);
        }
        
    }
}
