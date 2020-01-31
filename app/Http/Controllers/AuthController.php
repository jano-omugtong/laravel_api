<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\User;
use App\Notifications\SignupActivate;

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
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        
        $user = new User([
            'last_name' => $request->last_name,
            'first_name' => $request->first_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'sex' => isset($request->sex) ? $request->sex : 0,
            'civil_status' => isset($request->civil_status) ? $request->civil_status : 'N',
            'address' => $request->address,
            'nationality' => $request->nationality,
            'activation_token' => Str::random(60),
        ]);

        $user->save();

        $user->notify(new SignupActivate($user));
        
        return response()->json([
                'message' => 'You have signed up successfully!'
            ], 201);
    }

    /**
     * Create user
     *
     * @param  [string] token
     * @return [string] message
     * @return [json] user
     */
    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();
        
        if (!$user) {
            return response()->json([
                    'message' => 'This activation token is invalid.'
                ], 404);
        }

        $user->active = true;
        $user->activation_token = '';

        $user->save();

        return $user;
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
        $credentials['active'] = 1;
        $credentials['deleted_at'] = null;
        
        if (!Auth::attempt($credentials)){
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
     * Unauthorized user or Home guest show user list
     *
     * @return [string] message
     */
    public function home($tolist)
    {
        error_log(print_r($tolist, true));
        if ($tolist){
            return User::all()->makeHidden(['email', 'last_name'])->toArray();
        }

        return response()->json([
                'message' => 'Unauthorized'
            ], 401);
    }

    /**
     * Change the User Password
     *
     * @return [json] user object
     */
    public function change_pass(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'new_password' => 'required|string|confirmed'
        ]);        
                
        if(!Auth::guard('web')->attempt(['id' => Auth::id(), 'password' => $request->password]))
            return response()->json([
                    'message' => 'Incorrect Password'
                ], 401);
        
        $user = User::where('id', Auth::id())->first();
        $user->password = bcrypt($request->new_password);
        $user->save();    
        
        return response()->json([
                'message' => 'Password changed successfully.'
            ], 200);
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
            ], 200);
    }
  
}