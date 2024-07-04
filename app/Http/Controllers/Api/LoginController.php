<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Authorization;
use App\Models\Bussiness\TokenGenerator;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
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
        
        $model = new TokenGenerator;
        $token = $model->generateTokenFromCredential($credentials);


        // get user/client data and saving value
        $user = User::where('username', $request->username)->first();
        if($user->is_active == 0){
            return response()->json([
                'success' => false,
                'message' => 'User anda belum aktif, silahkan hubungi administrator untuk mengaktifkan user'
            ], 401);
        }

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
}
