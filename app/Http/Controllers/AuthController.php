<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function unauthorized()
    {
        return response()->json([
            'error' => 'unauthorized'
        ], 401);
    }

    public function register(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
        ]);
        if (!$validator->fails()) {
            $email = $request->input('email');
            $name = $request->input('name');
            $cpf = $request->input('cpf');
            $password = $request->input('password');


          

                $newUser = new User();
                $newUser->name = $name;
                $newUser->email = $email;
                $newUser->cpf = $cpf;
                $newUser->password = Hash::make($password);;
                $newUser->save();

                $token = auth()->attempt([
                    'cpf' => $cpf,
                    'password' => $password
                ]);
                if (!$token) {
                    $array['error'] = 'User or invalid password';
                    return $array;
                }
                $array['token'] = $token;

                $user = auth()->user();
                $array['user'] = $user;

                $properties = Unit::select(['id','name'])->where('id_owner', $user['id'])->get();
                $array['user']['properties']=$properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
