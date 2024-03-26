<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    //
    public function register(StoreRegisterRequest $request){
        $user = User::create($request->getData());

        return response()->json([
            'user' => $user,
            'message' => 'Account Succesfully created'
        ], Response::HTTP_CREATED);
    }
}
