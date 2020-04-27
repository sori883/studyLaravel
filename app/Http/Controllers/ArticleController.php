<?php

namespace App\Http\Controllers;

// Articleモデルを追加
use App\Article;
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
}
