<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class ApiController extends Controller
{
    //register, login, profile are the open methods that we can hit without user login
    //POST [name, email, password]
    public  function register(Request $request){
        // Validations
        $request->validate([
            "name"=>"required|string",
            "email"=>"required|string|email|unique:users",
            "password"=>"required|confirmed"
        ]);
        // User
        User::create([
            "name"=>$request->name,
            "email"=>$request->email,
            "password"=>bcrypt($request->password)
        ]);

        return response()->json([
            "status"=>true,
            "message"=>"User registered successfully",
            "data"=>[]
        ]);
    }

    //POST [email, password]
    public  function login(Request $request){
        // Validation 
        $request->validate([
            "email"=>"required|email|string",
            "password"=>"required"
        ]);
        // email check
        $user = User::where('email',$request->email)->first();
        if(!empty($user)){
            // User exists
            if(Hash::check($request->password, $user->password)){
                // password matched
                $token = $user->createToken('myToken')->plainTextToken;//for this line we have imported Laravel\Sanctum\HasApiTokens in User.php and imported in class as well 
                return response()->json([
                    "status"=>true,
                    "message"=>"User Logged In",
                    "token"=>$token,
                    "data"=>[]
                ]);
            }else{
                return response()->json([
                    "status"=>false,
                    "message"=>"Invalid password.",
                    "data"=>[]
                ]);
            }
        }else{
            return response()->json([
                "status"=>false,
                "message"=>"Please pass correct email.",
                "data"=>[]
            ]);
        }
        // Password Check

        // Auth Token   
    }

    // profile and logout are that we will use with user login. So, they are protected routes
    //GET [Auth: Token]
    public function profile(){
        $userData = auth()->user(); 

        return response()->json([
            "status"=>true,
            "message"=>"Profile Information",
            "dada"=>$userData,
            "id"=>auth()->user()->id  //if want only id
        ]);   
    }

    // GET [Auth: Token]
    public function logout(){
    //    in sthis method we will delete all generated tokens of the user
       auth()->user()->tokens()->delete();
       return response()->json([
        "status"=>true,
        "message"=>"User logged out",
        "data"=>[]
       ]);
    }
}
