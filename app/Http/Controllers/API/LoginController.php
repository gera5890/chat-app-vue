<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{

    public function login(LoginRequest $request){

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'The credentials are not correct',
                Response::HTTP_UNAUTHORIZED
            ]);
        }

        return response()->json([
            'message' => 'Login success',
            'user' => $user,
            'token' => $user->createToken('social_app_token')->plainTextToken 
        ], Response::HTTP_ACCEPTED);
    }

    public function getProfile(Request $request){
        return $request->user();
    }

    public function logout(Request $request){

        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout success'
        ], Response::HTTP_OK);
    }

    public function testMail(Request $request){
        $data = [
            'name' => 'Gerardo Vera',
            'body' => 'Esto es un texto de prueba, estamos enviando un email'
        ];
        Mail::to('geraveravela150b@gmail.com')->send(new TestMail('test subject', $data));
    }
}
