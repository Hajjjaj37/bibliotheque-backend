<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;


class Authentifacation extends Controller
{

    public function register(Request $request){
        if($request->has("image")){

            $file = $request->image;
            $fileName = time(). "_". $file->getClientOriginalName();
            $file->move("profil", $fileName);
        }

      if($request->password != null || $request->password != "" || !empty($request->password)){
        $user = new User();
        $user->name = $request->name;
        $user->username = $request->username;
        $user->adresse = $request->adresse;
        $user->email=$request->email;
        $user->password= Hash::make($request->password);
        $user->image= $fileName;
        $user->save();
      }else{
        return response()->json(["error"=>"password empty"]);
      }

        return response()->json([
            "message"=> "user added successfully"
        ]);

        return $request;



    }

    public function login(Request $request){


            $user = $request->only("email", "password");

            if(Auth::attempt(["email" => $user["email"], "password" => $user["password"]])){

                $userData = Auth::user();
                $expirationDateTime = Carbon::now()->addHours(24);
                $token = $userData->createToken("token", [], $expirationDateTime)->plainTextToken;

                $cookie = Cookie::make("token", $token, 1440);

                return response()->json(["message" => $token], 200);

            }else{
                return response()->json(["error" => "Invalid credentials"], 401);
            }
        }

        public function logout(){
            try{

                $user = Auth::user();
                $user->tokens()->delete();
                return response()->json([
                    "message" => "logout success"
                ])->cookie("token", null, -1);

            }catch(\Exception $e){
                return response()->json(["error" => $e->getMessage()], 400);
            }
        }
        public function user(){
            return Auth::user();
        }
}


