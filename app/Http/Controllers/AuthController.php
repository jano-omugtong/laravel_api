<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] last_name
     * @param  [string] first_name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [unsigned_tinyint] sex
     * @param  [char] civil_status
     * @param  [string] adress
     * @param  [string] nationality
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'last_name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        
        $user = new User([
            'last_name' => $request->last_name,
            'first_name' => isset($request->first_name) ? $request->first_name : ' ',
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'sex' => isset($request->sex) ? $request->sex : 0,
            'civil_status' => isset($request->civil_status) ? $request->civil_status : 'N',
            'address' => isset($request->address) ? $request->address : ' ',
            'nationality' => isset($request->nationality) ? $request->nationality : ' ',
        ]);

        $user->save();
        
        return response()->json([
                'message' => 'You have signed up successfully!'
            ], 201);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);        
        
        $credentials = request(['email', 'password']);
        
        if(!Auth::attempt($credentials)){
            return response()->json([
                    'message' => 'Login failed. Incorrect email or password.'
                ], 401);
        }
            
        $user = $request->user();
        
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        
        if ($request->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();
        
        return response()->json([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
            ]);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        
        return response()->json([
                'message' => 'Successfully logged out'
            ]);
    }
  
}