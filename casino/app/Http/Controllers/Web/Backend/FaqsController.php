<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class FaqsController extends \VanguardLTE\Http\Controllers\Controller
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
            $faqs = \VanguardLTE\Faq::orderBy('rank', 'ASC')->get();
            return view('backend.faqs.list', compact('faqs'));
        }
        public function create()
        {
            return view('backend.faqs.add');
        }
        public function store(\Illuminate\Http\Request $request)
        {
            $data = $request->only([
                'question', 
                'answer', 
                'rank'
            ]);
            \VanguardLTE\Faq::create($data);
            return redirect()->route('backend.faq.list')->withSuccess(trans('app.faq_created'));
        }
        public function edit(\VanguardLTE\Faq $faq)
        {
            return view('backend.faqs.edit', compact('faq'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Faq $faq)
        {
            $data = $request->only([
                'question', 
                'answer', 
                'rank'
            ]);
            $faq->update($data);
            return redirect()->route('backend.faq.list')->withSuccess(trans('app.faq_updated'));
        }
        public function delete(\VanguardLTE\Faq $faq)
        {
            \VanguardLTE\Faq::where('id', $faq->id)->delete();
            return redirect()->route('backend.faq.list')->withSuccess(trans('app.faq_deleted'));
        }
        public function security()
        {
        }
    }

}
