<?php

namespace App\Http\Controllers;


use App\Article; // Articleモデル
use App\Http\Requests\ArticleRequest; // フォームリクエスト
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        // モデルクラスが持つall()メソッドを使用して、Articleモデルから全データをコレクションで取得
        // 更に、コレクションメソッドのcreated_atで新しい記事順に並び替えて$articlesに格納
        $articles = Article::all()->sortByDesc('created_at');
 
        // laravel-sns\laravel\resources\views\articles\indexを参照する
        return view('articles.index', ['articles' => $articles]);
    }

    // 記事登録画面表示用
    public function create()
    {
        return view('articles.create');    
    }

    // 記事登録用
    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();
        return redirect()->route('articles.index');
    }
}
