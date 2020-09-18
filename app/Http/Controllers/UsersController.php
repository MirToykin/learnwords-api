<?php
namespace App\Http\Controllers;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    try {
      $this->validate($request, [
        'name' => 'required',
        'email' => 'required|email',
        'password' => 'required',
        'c_password' => 'required|same:password',
      ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json(['error'=>$e], 401);
    }

    $input = $request->all();
    $input['password'] = bcrypt($input['password']);
    $user = User::create($input);
    $user['token'] =  $user->createToken('LWToken')-> accessToken;
    return response()->json(['user'=>$user], $this-> successStatus);
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
