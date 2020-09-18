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
}
