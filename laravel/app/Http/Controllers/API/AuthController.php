<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function index()
    {
        $users = User::all();
        return $this->sendResponse($users, 'Displayed all users data');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
        $success['email'] = $user->email;
        return $this->sendResponse($success, 'User registered successfully!');
    }

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyAuthApp')->plainTextToken;
            $success['email'] = $user->email;
            return $this->sendResponse($success, 'User login successfully!');
        }
        return $this->sendError('Unauthorised', ['error' => 'Unauthorised']);
    }
}
