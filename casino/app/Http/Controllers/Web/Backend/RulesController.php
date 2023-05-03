<?php 
namespace VanguardLTE\Http\Controllers\Web\Backend
{
    include_once(base_path() . '/app/ShopCore.php');
    include_once(base_path() . '/app/ShopGame.php');
    class RulesController extends \VanguardLTE\Http\Controllers\Controller
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
            $rules = \VanguardLTE\Rule::orderBy('id', 'DESC')->get();
            return view('backend.rules.list', compact('rules'));
        }
        public function edit(\VanguardLTE\Rule $rule)
        {
            return view('backend.rules.edit', compact('rule'));
        }
        public function update(\Illuminate\Http\Request $request, \VanguardLTE\Rule $rule)
        {
            $data = $request->only([
                'title', 
                'keywords', 
                'description', 
                'text'
            ]);
            if( isset($data['text']) ) 
            {
                $data['text'] = str_replace('&nbsp;', ' ', $data['text']);
                $data['text'] = str_replace('<br>', '', $data['text']);
            }
            $rule->update($data);
            return redirect()->route('backend.rule.list')->withSuccess(trans('app.rule_updated'));
        }
        public function security()
        {
        }
    }

}
