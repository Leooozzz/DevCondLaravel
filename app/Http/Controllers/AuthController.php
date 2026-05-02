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

    //Unauthorized function ->name('login')
    public function unauthorized()
    {
        return response()->json([
            'error' => 'unauthorized'
        ], 401);
    }

    //Register function()
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

            $properties = Unit::select(['id', 'name'])->where('id_owner', $user['id'])->get();
            $array['user']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    //Login function()
    public function login(Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required'
        ]);
        if (!$validator->fails()) {
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);
            if (!$token) {
                $array['error'] = 'Cpf or invalid password';
                return $array;
            }
            $array['token'] = $token;

            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])->where('id_owner', $user['id'])->get();
            $array['user']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    //Validate tokenJWT
    public function valideteToken()
    {
        $array = ['error' => ''];

        $user = auth()->user();
        $array['user'] = $user;

        $properties = Unit::select(['id', 'name'])->where('id_owner', $user['id'])->get();

        $array['user']['property'] = $properties;

        return $array;
    }
    //Invalidate tokenJWT
    public function logout()
    {
        $array = ['error' => ''];

        auth()->logout();

        return $array;
    }
}
