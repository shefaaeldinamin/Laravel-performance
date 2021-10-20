<?php

namespace App\Http\Controllers\API;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignUpRequest;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
   
    public function signup(SignUpRequest $request)
    {
        $data = $request->validated();
        $data['image'] = $data['image']->store('avatars');
        $data['password'] =  Hash::make($data['password']);

        $user = User::create($data);
        $token = $user->createToken('auth-token');
        
        event(new UserRegistered($user));       
        return (new UserResource($user))->additional([
            'auth_token' => $token->plainTextToken
        ]);
    }

    
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'message' => __('lang.invalid_credientials'),
            ]);
        }

        return response()->json([
            'auth_token' =>  $user->createToken('auth-token')->plainTextToken
        ]);
    }

    
    public function follow(User $user,Request $request)
    {
        $currentUser = $request->user();
        $followingUser = $user;

        $followings = $currentUser->followings();
        if ($followings->where('user_id', $request->user_id)->first()) {
            return response()->json([
                'message' => __('lang.followed_already', ['name' => $followingUser->name])
            ],404);
        }

        $followings->attach($followingUser);

        return response()->json([
            'message' =>  __('lang.followed_successfully', ['name' => $followingUser->name])
        ]);
    }
}
