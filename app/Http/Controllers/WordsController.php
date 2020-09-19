<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\countOf;

class WordsController extends Controller
{
  public $successStatus = 200;

  /**
   * Get next api
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getWords($category, $id)
  {
    $words = Word::where('category', $category)->where('user_id', $id)->get();
    return response()->json(['words' => $words], $this->successStatus);
  }

  /**
   * Add word api
   *
   * @param Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function addWord(Request $request)
  {
    try {
      $this->validate($request, [
        'title' => 'required',
        'meanings' => 'required',
        'category' => 'required',
        'user_id' => 'required',
      ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json(['error'=>$e], 401);
    }

    $input = $request->all();
    $word = Word::create($input);
    return response()->json(['word'=>$word], 201);
  }
}
