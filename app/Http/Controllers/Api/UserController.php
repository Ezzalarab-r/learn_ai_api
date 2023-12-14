<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// set_error_handler(function ($errno, $errstr, $errfile, $errline) {
//     http_response_code(200);
//     echo json_encode(['error' => $errstr]);
//     exit();
// });


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */

    public function signIn(Request $request)
    {
        // return response()->json([
        //     'status' => true,
        //     'message' => 'test message',
        //     'data' => 'test data',
        // ]);
        try {
            // Validate
            $validateUser = Validator::make(
                $request->all(),
                [
                    'avatar' => 'required',
                    'type' => 'required',
                    'open_id' => 'required',
                    'name' => "required",
                    'email' => "required",
                    // 'password' => 'required|min:6',

                ],
            );


            if ($validateUser->fails()) {
                return response()->json([
                    "status" => false,
                    "message" => "validation error",
                    "data" => $validateUser->errors(),
                ], 401,);
            }


            // $validated variable will have all the user fields
            $validated = $validateUser->validated();
            $userMap = [];
            // email, google, facebook or apple login
            $userMap["type"] = $validated["type"];
            // unique id from google login
            $userMap["open_id"] = $validated["open_id"];

            // return response()->json([
            //     'status' => true,
            //     'message' => 'checking',
            //     'data' => $userMap,
            // ], 200);

            $user = User::where($userMap)->first();

            // Check if user has allready registered or not
            if (empty($user->id)) {
                $validated['token'] = md5(uniqid() . rand(10000, 99999));
                // user first time registered
                $validated['created_at'] = Carbon::now();

                // returns the id of the row inserted
                $userID = User::insertGetId($validated);

                // encrypt password
                // $validated['password'] = Hash::make($validated['password']);

                $userInfo = User::where('id', '=', $userID)->first();

                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;
                $userInfo->access_token = $accessToken;

                // update the token in the database
                User::where('id', '=', $userID)->update(['access_token' => $accessToken]);

                return response()->json([
                    "status" => true,
                    "message" => "User Craeted Successfully",
                    "data" => $userInfo,
                ], 200,);
            }

            // user already registered
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated["open_id"])->update(['access_token' => $accessToken]);

            // $user = User::create([
            //     "name" => $request->name,
            //     "email" => $request->email,
            //     "password" => Hash::make($request->password),
            // ]);

            return response()->json([
                "status" => true,
                "message" => "User Loged in Successfully",
                "data" => $user,
            ], 200,);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
            ], 500);
        }
    }

    // /**
    //  * Login The User
    //  * @param Request $request
    //  * @return User
    //  */

    // public function loginUser(Request $request)
    // {
    //     try {
    //         $validateUser = Validator::make(
    //             $request->all(),
    //             [
    //                 "email" => "required|email",
    //                 "password" => "required"
    //             ],
    //         );
    //         if ($validateUser->fails()) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "validation error",
    //                 "errors" => $validateUser->errors()
    //             ], 401,);
    //         }

    //         if (!Auth::attempt($request->only(["email", "password"]))) {
    //             return response()->json([
    //                 "status" => false,
    //                 "message" => "Email and Password does not match with our record.",
    //             ], 401,);
    //         }

    //         $user = User::where("email", $request->email)->first();

    //         return response()->json([
    //             "status" => true,
    //             "message" => "User Logged In Successfully",
    //             "user" => $user->createToken("API TOKEN")->plainTextToken,
    //         ], 200,);
    //         //
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             "status" => false,
    //             "message" => $th->getMessage(),
    //         ], 500,);
    //     }
    // }
}
