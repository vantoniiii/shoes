<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        try{
           $request->validate([
               'name' => ['required','string','max:255'],
               'username' => ['required','string','max:255','unique:users'],
               'email' => ['required','string','max:255','email','unique:users'],
               'password' => ['required','string', new Password()],
               'phone' => ['required','string','max:255'],
           ]);

           User::create([
               'name' => $request->name,
               'username' => $request->username,
               'email' => $request->email,
               'password' => Hash::make($request->password),
               'phone' => $request->phone_number,
           ]);

           $user = User::where('email',$request->email)->first();

           $tokenresult = $user->createToken('authToken')->plainTextToken;

           return response()->json(['message'=>'Data berhasil dibuat',
                                    'Akses Token' => $tokenresult,
                                    'Token Type'=>'Bearer',
                                    'data'=>$user],200);
        }
        catch (\Exception $error){
            return response()->json(['message'=>'Data gagal dibuat','error'=>$error],500);
        }

    }

    public function login(Request $request)
    {


        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email','password']); //Ditampung variabel untuk auth:attempt

            if(!Auth::attempt($credentials)){             //Kondisi jika Auth gagal
                return \response()->json(['message'=>'Login gagal'],500);
            }

            $user = User::where('email',$request->email)->first();

            if(!Hash::check($request->password,$user->password,[])){        //Untuk cocokin password
                throw new \Exception('Invalid');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return \response()->json(['message'=>'Login berhasil',
                                     'Token Type'=>'Bearer',
                                     'Token'=>$tokenResult,
                                     'data'=>$user],200);
        }

        catch (\Exception $error){
            return \response()->json(['message'=>'Login gagal','error message'=>$error],500);
        }
    }

    public function fetch(Request $request)
    {
        $datauser = $request->user();
        return \response()->json(['message'=>'Profil User berhasil diambil',
                                    $datauser]);
    }

    public function editprofil(Request $request){
        try {

             $data = $request->all();   //Request semua inputan

             $user = Auth::user();      //Untuk auth user yang lagi login
             $user -> update($data);

            return \response()->json(['message'=>'Profil berhasil diubah',
                                      'data'=>$user]);
        }

        catch (\Exception $error){
            return \response()->json(['message' => 'Profil gagal diubah',
                                        'error' => $error]);
        }

    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete(); //Untuk ambil token user yg lagi login

        return \response()->json(['message'=>'Berhasil Logout','Token Revoked'=>$token]);
    }
}
