<?php

namespace App\Http\Controllers;

use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
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
    $messages = [
      'title.required' => 'Необходимо ввести слово',
      'meanings.required' => 'Необходимо ввести значения',
    ];
    $validator = Validator::make($request->all(), [
      'title' => 'required|string',
      'meanings' => 'required|string',
      'category' => 'required|string',
      'user_id' => 'required'
    ], $messages);

    if ($validator->fails()) {
      return response()->json(['message' => $validator->errors()->first(), 'status' => false], 500);
    }

    $title = $request['title'];
    $uid = $request['user_id'];
//    $cat = $request['category'];
//    $set = $cat === 'next' ? 'На очереди' : $cat === 'current' ? 'Текущий набор' : 'Изученные';
//
//    if (Word::where('user_id', $uid)->where('title', $title)->count() > 0) {
//      return response()->json(['message'=>'Слово '.$title.' уже есть в наборе '.$set], 400);
//    }

    $existingWord = Word::where('user_id', $uid)->where('title', $title)->first();
    if (isset($existingWord)) {
      $cat = $existingWord['category'];
      $set = $cat === 'next' ? 'На очереди' : $cat === 'current' ? 'Текущий набор' : 'Изученные';
      return response()->json(['message'=>'Слово "'.strtolower ($title).'" уже есть в наборе "'.$set.'"'], 400);
    }

    $input = $request->all();
    $word = Word::create($input);
    return response()->json(['word'=>$word], 201);
  }

  /**
   * Edit word api
   *
   * @param Request $request
   * @param $id
   * @return \Illuminate\Http\JsonResponse
   */

  public function editWord(Request $request, $id) {
    try {
      $word = Word::find($id);
      $isUpdated = $word->update($request->all());
      if ($isUpdated) {
        return response()->json(['word'=>$word], 200);
      } else {
        throw new Exception('Не удалось внести изменения', 400);
      }
    } catch (Exception $e) {
      return response()->json(['error'=>$e]);
    }
  }

  /**
   * Delete word api
   *
   * @param $id
   * @return \Illuminate\Http\JsonResponse
   */

  public function deleteWord($id) {
    try {
      $word = Word::find($id);

      if ($word->delete() === 1) {
        return response()->json(['success'=>'Запись успешно удалена'], 204);
      } else {
        throw new Exception('Не удалось удалить запись', 400);
      }
    } catch (Exception $e) {
      return response()->json(['error'=>$e]);
    }
  }
}
