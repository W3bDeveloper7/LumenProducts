<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param Request $request
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
        {
            //validate incoming request
            $this->validate($request, [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required',
            ]);

            try {

                $user = User::create($request->all());

                //return successful response
                return response()->json(['data' => $user, 'message' => 'CREATED'], 201);

            } catch (\Exception $e) {
                //return error message
                return response()->json(['message' => 'User Registration Failed!'], 409);
            }

        }

    public function login(Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    //logout user
    public function logout()
    {
        auth()->logout();
        return $this->responseJson('Successfully logged out',200);
    }

    // refresh token
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


    //Add this method to the Controller class
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'account' => auth()->user()
        ], 200);
    }
}
