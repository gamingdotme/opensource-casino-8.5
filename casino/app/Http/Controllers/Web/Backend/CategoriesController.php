<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class CategoriesController extends \VanguardLTE\Http\Controllers\Controller
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
            $categories = \VanguardLTE\Category::where(['parent' => 0])->orderBy('position')->get();
            return view('backend.categories.list', compact('categories'));
        }
        public function create()
        {
            $categories = \VanguardLTE\Category::where(['parent' => 0])->pluck('id', 'title')->toArray();
            $categories = array_merge(['Root' => 0], $categories);
            $categories = array_flip($categories);
            return view('backend.categories.add', compact('categories'));
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->all();
            $category = \VanguardLTE\Category::create($data);
            return redirect()->route('backend.category.list')->withSuccess(trans('app.category_created'));
        }
        public function edit($category)
        {
            $category = \VanguardLTE\Category::where('id', $category)->first();
            $categories = \VanguardLTE\Category::where(['parent' => 0])->pluck('id', 'title')->toArray();
            $categories = array_merge(['Root' => 0], $categories);
            $categories = array_flip($categories);
            return view('backend.categories.edit', compact('category', 'categories'));
        }
        public function update(\VanguardLTE\Category $category, \Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'title', 
                'parent', 
                'position', 
                'href'
            ]);
            \VanguardLTE\Category::where('id', $category->id)->update($data);
            return redirect()->route('backend.category.list')->withSuccess(trans('app.category_updated'));
        }
        public function delete(\VanguardLTE\Category $category)
        {
            \VanguardLTE\GameCategory::where('category_id', $category->id)->delete();
            $category = \VanguardLTE\Category::where('id', $category->id)->delete();
            return redirect()->route('backend.category.list')->withSuccess(trans('app.category_deleted'));
        }
        public function security()
        {
        }
    }

}
