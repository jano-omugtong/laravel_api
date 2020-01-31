<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\User;
use App\Notifications\SignupActivate;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        if ($users){
            return $users;
        }

        return response()->json([
                'message' => 'No data'
            ], 404);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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
                'message' => 'User created successfully!'
            ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if ($user){
            return $user;
        }

        return response()->json([
                'message' => 'User not found'
            ], 404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if ($user){

            $user->last_name = isset($request->last_name) ? $request->last_name : null;
            $user->first_name = isset($request->first_name) ? $request->first_name : null;
            $user->sex = isset($request->sex) ? $request->sex : 0;
            $user->civil_status = isset($request->civil_status) ? $request->civil_status : 'N';
            $user->address = isset($request->address) ? $request->address : null;
            $user->nationality = isset($request->nationality) ? $request->nationality : null;
            
            $user->save();

            return response()->json([
                    'message' => 'Successfully updated user!'
                ], 200);
        }

        return response()->json([
                'message' => 'User not found'
            ], 404);

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user){
            $user->delete();

            return response()->json([
                    'message' => 'Successfully deleted user!'
                ], 200);
        }

        return response()->json([
                'message' => 'User not found'
            ], 404);

    }
}
