<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class ArticlesController extends \VanguardLTE\Http\Controllers\Controller
    {
        public function __construct()
        {
            $this->middleware([
                'auth', 
                '2fa'
            ]);
            $this->middleware('permission:access.admin.panel');
        }
        public function index(\Illuminate\Http\Request $request)
        {
            $articles = \VanguardLTE\Article::orderBy('id', 'DESC')->get();
            return view('backend.articles.list', compact('articles'));
        }
        public function create()
        {
            return view('backend.articles.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'title', 
                'keywords', 
                'description', 
                'text'
            ]);
            \VanguardLTE\Article::create($data);
            return redirect()->route('backend.article.list')->withSuccess(trans('app.article_created'));
        }
        public function edit(\VanguardLTE\Article $article)
        {
            return view('backend.articles.edit', compact('article'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Article $article)
        {
            $data = $request->only([
                'title', 
                'keywords', 
                'description', 
                'text'
            ]);
            $article->update($data);
            return redirect()->route('backend.article.list')->withSuccess(trans('app.article_updated'));
        }
        public function delete(\VanguardLTE\Article $article)
        {
            \VanguardLTE\Article::where('id', $article->id)->delete();
            return redirect()->route('backend.article.list')->withSuccess(trans('app.article_deleted'));
        }
        public function security()
        {
        }
    }

}
