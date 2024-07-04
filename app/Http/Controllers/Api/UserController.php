<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;



class UserController extends Controller
{
    public function register(Request $request)
    {

        //set validation
        $validator = Validator::make($request->all(), [
            'username'      => 'required|unique:users',
            'password'  => 'required|min:6|confirmed',
            'role'  => 'required|in:superadmin,user'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $user = new User();
        $user->username = $request->username;
        $user->role = $request->role;;
        $user->password = bcrypt($request->password);
        $user->save();

        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }

    public function activate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'     => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('username', $request->username)->first();
        if(!$user){
            return response()->json([
                'success' => false,
                'message' => "User No Found"
            ], 404);
        }

        $user-> is_active = 1;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "User Activated"
        ], 200);
    }

    public function getAll(Request $request)
    {
        // $token = JWTAuth::getToken();
        // $authorized = GlobalFunction::userScope($token,'user','read');
        // if(!$authorized){
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'User not allowed'
        //     ], 403);
        // }

        $user = User::select('username', 'role','id')
        // ->where('is_active', 1)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function getAllPending(Request $request)
    {
       
        $user = User::select('username', 'role','id')
        ->where('is_active', 0)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function storeUpdate(Request $request)
    {

        //set validation
        $validator = Validator::make($request->all(), [
            'username'      => 'required|unique:users',
            'role'  => 'required|in:superadmin,user'
        ]);
    
        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('id', $request->id)->first();
        $user->username = $request->username;
        $user->role = $request->role;
        $user->save();



        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
}
