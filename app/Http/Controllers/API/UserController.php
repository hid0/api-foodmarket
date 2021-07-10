<?php

namespace App\Http\Controllers\API;

use App\Actions\Fortify\PasswordValidationRules;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash};

class UserController extends Controller
{

  use PasswordValidationRules;

  public function login(Request $request)
  {
    try {
      // ? input validation
      $request->validate([
        'email' => 'email|required',
        'password' => 'required'
      ]);
      // credentials checkup
      $authLogin = request(['email', 'password']);
      if (!Auth::attempt($authLogin)) {
        return ResponseFormatter::error([
          'message' => 'Unauthorization'
        ], 'Authentication Failed', 500);
      }

      // ? if password false show error
      $user = User::where('email', $request->email)->first();
      if (!Hash::check($request->password, $user->password, [])) {
        throw new Exception('Invalid Credentials');
      }
      // ? if succes then logged in
      $tokenRes = $user->createToken('authToken')->plainTextToken;
      return ResponseFormatter::success([
        'access_token' => $tokenRes,
        'token_type' => 'Bearer',
        'user' => $user,
      ], 'Authenticated');
    } catch (Exception $err) {
      return ResponseFormatter::error([
        'message' => 'something wrong'
      ], 'Authenticated failed', 500);
    }
  }

  public function register(Request $request)
  {
    try {
      $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => $this->passwordRules(),
      ]);
      // ? insert register user to database
      User::create([
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        'houseNumber' => $request->houseNumber,
        'phoneNumber' => $request->phoneNumber,
        'city' => $request->city,
        'password' => Hash::make($request->password),
      ]);
      $user = User::where('email', $request->email)->first();
      $tokenRes = $user->createToken('authToken')->plainTextToken;
      return ResponseFormatter::success([
        'access_token' => $tokenRes,
        'token_type' => 'Bearer',
        'user' => $user,
      ]);
    } catch (Exception $err) {
      return ResponseFormatter::error([
        'message' => 'something wrong'
      ], 'Authenticated failed', 500);
    }
  }

  public function logout(Request $request)
  {
    $token = $request->user()->currentAccessToken()->delete();

    return ResponseFormatter::success($token, 'Token Revoked');
  }

  public function updateProfile(Request $request)
  {
    // 
  }
}
