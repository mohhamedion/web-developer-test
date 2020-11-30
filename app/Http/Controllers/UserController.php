<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Hash;
use App\User; 
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\LoginUserRequest;

class UserController extends Controller
{

    public function register(StoreUserRequest $request)
    {     
        try 
        {
            $user['email'] = $request->email;
            $user['name'] = $request->name;
            $user['password'] = Hash::make($request->password);
            $user['coin'] = 100;
            $user = User::create($user);
            $token = $user->createToken('TestTest')->accessToken;
            return response()->json(['token'=>$token,'user'=>$user]);
        } catch (\Throwable $th)
        {
            return response()->json(['success'=>false,'error'=>$th->getMessage()]);
        }

    }

    public function login(LoginUserRequest $request)
    {
        $user = User::where('email',$request->email)->first();
        if($user){
            if(Hash::check($request->password, $user->password)){
                $token=  $user->createToken('TestTest')->accessToken;
                return response()->json(['token'=>$token,'user'=>$user]);
            } 
        }
        return response()->json(['error'=>['email'=>['incorrect data']]],401);
    }
        
}




