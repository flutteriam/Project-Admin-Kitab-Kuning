<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\PostBab;
use App\Models\Category;
use App\Models\PostBait;
use App\Models\PostKata;
use App\Models\PostKataVar;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\NahwuRequest;

class NahwuController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_post = null;
        $post = null;
        $first_bait = null;
        $active_category = null;
        if($id) {
            $bab = PostBab::with('post')->find($id);
            $temp_post = Post::find($bab->post_id);
            $post = Post::where('category_id', $temp_post->category_id)->get();
            $active_category = Category::find($post->first()->category_id);
            $first_bait = PostBait::where('bab_id', $bab->id)->orderBy('no', 'ASC')->first()->id;
        }
        return view('post::nahwu', compact('categories', 'bab', 'temp_post', 'post', 'active_category', 'first_bait'));
    }

    public function ajax_bab_detail($bab_id) {
        $babs = PostBab::where('id', $bab_id)->with(['bait.kata', 'bait.kata.vars'])->orderBy('no', 'ASC')->get();
        return view('post::ajax_bab_detail_nahwu', compact("babs"));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    // public function store(ChapterCreateRequest $request)
    // {
    //     $last_no = PostKata::where('bab_id', $request->bab_id)->where('bait_id', $request->bait_id)->latest()->first()->no ?? 0;
    //     $request->merge([
    //         'no' => ($last_no + 1)
    //     ]);
    //     PostKata::create($request->all());
    //     return response()->json([
    //         'status' => true,
    //         'message' => [
    //             'head' => 'Berhasil',
    //             'body' => 'Menambahkan Kata baru'
    //         ]
    //     ], 200);
    // }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        // Id => id bait bukan id kata
        return response()->json([
            'status' => true,
            'data' => PostKata::where('bait_id', $id)->orderBy('no', 'ASC')->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $kata = PostKata::with(['vars' => function($sub) {
            $sub->with(['sugestAdmin', 'sugestUser']);
        }, 'vars.tags'])->find($id);
        return response()->json([
            'status' => true,
            'data' => $kata
        ],200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(NahwuRequest $request, $id)
    {
        // dd($request->input());
        // $data_from_request = [
        //     'kata_id' => 1,
        //     'var' => [],
        //     'tags' => [[],[]]
        // ];
        $data_from_request = $request->input();

        DB::beginTransaction();
        try {
            // $arr_var_id = PostKataVar::where('kata_id', $id)->get()->pluck('id')->toArray();

            // DB::table('post_kata_var_tag')->whereIn('var_id', $arr_var_id)->delete();

            if(session('role') == 'admin') {
                $attach = [
                    'verified_at' => date('Y-m-d H:i:s'),
                    'verified_by' => session('user_id')
                ];
            } else if(session('role') == 'contributor') {
                $attach = [
                    'suggested_by' => session('key')
                ];
            }

            // PostKataVar::where('kata_id', $id)->delete();

            foreach ($data_from_request['var'] as $key_var => $var) {
                $input = [
                    'kata_id' => $id,
                    'var' => $var,
                ];
                $merge = array_merge($input, $attach);
                $checkPostKataVar = PostKataVar::where('kata_id', $id)->where('var', $var)->first();
                if($checkPostKataVar) {
                    $checkPostKataVar->update([
                        'var' => $var
                    ]);
                    $save_var = PostKataVar::where('kata_id', $id)->where('var', $var)->first();
                } else {
                    $save_var = PostKataVar::create($merge);
                }

                $tags = explode(',', $data_from_request['tags'][$key_var]);

                $save_var->tags()->syncWithoutDetaching($tags, $attach);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => [
                    'head' => 'Berhasil',
                    'body' => 'Manambahkan Nahwu'
                ]
            ], 200);
        } catch (\Exception $th) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => [
                    'head' => 'Gagal',
                    'body' => $th->getMessage()
                ]
            ], 500);
        }
    }

    public function updateVerified(Request $request) {
        $postKataVar = PostKataVar::find($request->id);
        $postKataVar->update([
            'verified_at' => date('Y-m-d H:i:s'),
            'verified_by' => session('user_id')
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Verfied nahwu'
            ]
        ], 200);
    }
}
