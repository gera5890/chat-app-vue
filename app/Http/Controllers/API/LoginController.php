<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Mail\ForgotPasswordEmail;
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

    public function forgetPasswordReset(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'errors' => ['email' => ['Account with this email not found']]
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $code = rand(11111,99999);
 
        $user->remember_token = $code;
        $user->save();

        $body = [
            'name' => $user->first_name .' '. $user->last_name,
            'code' => $code
        ];

        Mail::to($user->email)->send(new ForgotPasswordEmail('Forgot Password Request', $body));

        return response()->json([
            'message' => 'We have sended code to your email'
        ]);

    }

    public function verifyAndChangePassword(Request $request){

        $request->validate([
            'email' => 'required|email',
            'code' => 'required|integer',
            'password' => 'required|confirmed'
        ]);

        $user = User::where('email', $request->email)
                        ->where('remember_token', $request->code)
                        ->first();
        
        if(!$user){
            return response()->json([
                'errors' => ['code' => 'Invalid otp']
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->remember_token = null;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password succesfully changed'
        ], Response::HTTP_ACCEPTED);
    }

    public function changePassword(Request $request){
        $request->validate([
            'email' => 'required|email',
            'old_password' => 'required',
            'password' => 'required|confirmed|min:8|max:16'
        ]);


        $user = $request->user();

        if(!Hash::check($request->old_password,  $user->password)){
            return response()->json([
                'message' => 'The old password is not correct'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'message' => 'Password updated succesfully'
        ], Response::HTTP_ACCEPTED);

    }

    public function updateProfile(Request $request){
        $request->validate([
            'first_name' => 'required|string|min:1|max:255',
            'last_name' => 'required|string|min:1|max:255'
        ]);

        $user = $request->user();

        if($user->email != $request->email){
            $request->validate([
                'email' => 'required|email|unique:users,email'
            ]);
        }

        $user->first_name = $request->first_name ?? $user->first_name;
        $user->last_name = $request->last_name ?? $user->last_name;

        $user->save();

        return response()->json([
            'messages' => 'Profile updated succesfully'
        ]);
    }
}
