<?php
namespace App\Http\Controllers;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;

class UsersController extends Controller
{
  public $successStatus = 200;
  /**
   * login api
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(){
    if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
      $user = Auth::user();
      $user['token'] =  $user->createToken('LWToken')-> accessToken;
      return response()->json(['user' => $user], $this-> successStatus);
    }
    else{
      return response()->json(['error'=>'Unauthorised', 'code' => '401'], 401);
    }
  }

  /**
   * Register api
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    $messages = [
      'name.required' => 'Введите имя',
      'email.required' => 'Необходимо указать Email',
      'email.email' => 'Некорректный Email',
      'email.unique' => 'Данный Email уже занят',
      'password.required' => 'Введите пароль',
      'password_confirmation.same' => 'Пароли не совпадают',
    ];
    $validator = Validator::make($request->all(), [
      'name' => 'required|string',
      'email' => 'required|email|unique:users',
      'password' => 'required|string',
      'password_confirmation' => 'required|string|same:password'
    ], $messages);

    if ($validator->fails()) {
      return response()->json(['message' => $validator->errors()->first(), 'status' => false], 500);
    }
    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $user['token'] =  $user->createToken('LWToken')-> accessToken;
    return response()->json(['user'=>$user], 201);
  }

  public function logout()
  {
    try {
      if (Auth::check()) {
        Auth::user()->OauthAccessToken()->delete();
        return response()->json(['message'=>'successfully logged out'], 200);
      }
    } catch (Exception $e) {
      return response()->json(['error'=>$e], 400);
    }

  }
//  /**
//   * details api
//   *
//   * @return \Illuminate\Http\JsonResponse
//   */
//  public function details()
//  {
//    $user = Auth::user();
//    return response()->json(['success' => $user], $this-> successStatus);
//  }
}
