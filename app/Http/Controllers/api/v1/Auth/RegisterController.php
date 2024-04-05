<?php

namespace App\Http\Controllers\api\v1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Register
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 500);
        }
        $emailValidation = User::where('email', $request->email)->first();
        if (is_null($emailValidation) || !$emailValidation) {
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'type' => 1,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);
            return response()->json(['user' => $user, 'token' => $token, 'status' => 200], 200);
        }
        $response = [
            'success' => false,
            'message' => 'Email is already taken',
            'status' => 500
        ];
        return response()->json($response, 500);
    }

    public function create_admin_account(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 500);
        }
        $emailValidation = User::where('email', $request->email)->first();
        if (is_null($emailValidation) || !$emailValidation) {
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'type' => 0,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);
            return response()->json(['user' => $user, 'token' => $token, 'status' => 200], 200);
        }
        $response = [
            'success' => false,
            'message' => 'Email is already taken',
            'status' => 500
        ];
        return response()->json($response, 500);
    }
}
