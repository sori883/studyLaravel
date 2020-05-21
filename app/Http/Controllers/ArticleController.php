<?php

namespace App\Http\Controllers;


use App\Article; // Articleモデル
use App\Tag;
use App\Http\Requests\ArticleRequest; // フォームリクエスト
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Article::class, 'article');
    }

    public function index()
    {
        // モデルクラスが持つall()メソッドを使用して、Articleモデルから全データをコレクションで取得
        // 更に、コレクションメソッドのcreated_atで新しい記事順に並び替えて$articlesに格納
        $articles = Article::all()->sortByDesc('created_at')
                                            ->load(['user', 'likes', 'tags']); 
 
        // laravel-sns\laravel\resources\views\articles\indexを参照する
        return view('articles.index', ['articles' => $articles]);
    }

    // 記事登録画面表示用
    public function create()
    {
        $allTagNames = $this->AllTagName();
 
        return view('articles.create', [
            'allTagNames' => $allTagNames,
        ]);
    }

    // 記事登録用
    public function store(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all());
        $article->user_id = $request->user()->id;
        $article->save();
        
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    public function edit(Article $article)
    {
        $tagNames = $article->tags->map(function ($tag) {
            return ['text' => $tag->name];
        });

        $allTagNames = $this->AllTagName();

        return view('articles.edit', [
            'article' => $article,
            'tagNames' => $tagNames,
            'allTagNames' => $allTagNames,
        ]);
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article->fill($request->all())->save();

        $article->tags()->detach();
        $request->tags->each(function ($tagName) use ($article) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $article->tags()->attach($tag);
        });

        return redirect()->route('articles.index');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('articles.index');
    }

    public function show(Article $article)
    {
        return view('articles.show', ['article' => $article]);
    }

    public function like(Request $request, Article $article)
    {
        // 1人のユーザーが同一記事に複数回いいね出来ないようにdetach（削除）を実行
        $article->likes()->detach($request->user()->id);
        $article->likes()->attach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    public function unlike(Request $request, Article $article)
    {
        $article->likes()->detach($request->user()->id);

        return [
            'id' => $article->id,
            'countLikes' => $article->count_likes,
        ];
    }

    private function AllTagName()
    {
        return Tag::all()->map(function ($tag) {
            return ['text' => $tag->name];
        });
    }

}
