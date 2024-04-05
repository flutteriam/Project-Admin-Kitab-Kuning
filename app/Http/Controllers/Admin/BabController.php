<?php

namespace App\Http\Controllers\Admin;

use App\Models\PostBab;
use App\Models\PostKata;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Http\Requests\BabCreateRequest;
use Http\Requests\BabUpdateRequest;

class BabController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $active_post = null;
        $all_post = null;
        if($id) {
            $active_post = Post::with('category')->find($id);
            $all_post = Post::where('category_id', $active_post->category_id)->get();
        }
        return view('post::bab', compact("categories", "active_post", "all_post"));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BabCreateRequest $request)
    {
        $last_no = PostBab::where('post_id', $request->post_id)->latest()->first()->no ?? 0;
        $request->merge([
            'no' => ($last_no + 1)
        ]);
        PostBab::create($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Bab baru'
            ]
        ], 200);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return response()->json([
            'status' => true,
            'data' => PostBab::where('post_id', $id)->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return response()->json([
            'status' => true,
            'data' => PostBab::with('post')->find($id)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BabUpdateRequest $request, $id)
    {
        // $request->merge(['post_id' => $request('post_id_yy')]);

        $bab = PostBab::find($id);
        $bab->update($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah Bab'
            ]
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $bab = PostBab::find($id);
        $lower_bab = PostBab::where('no', '>', $bab->no)->get();
        foreach ($lower_bab as $key => $value) {
            $temp_no = $value->no;
            $value->update([
                'no' => ($temp_no - 1)
            ]);
        }
        $bab->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus bab'
            ]
        ], 200);
    }

    public function ajax_post_detail($post_id) {
        $babs = PostBab::where('post_id', $post_id)->with(['bait.kata' => function($q){
            $q->orderBy('no', 'ASC');
        }])->get();

        return view('post::ajax_bab_detail', compact("babs"));
    }

    public function datatable(Request $request) {
        $post_id = $request->post;
        $bab = PostBab::where('post_id', $post_id)->with(['post', 'bait.kata' => function($q){
            $q->orderBy('no', 'ASC');
        }])->get();

        // $bab = PostBab::when($post_id, function($q) use($post_id) {
        //     $q->where('post_id', $post_id);
        // })->with('post')->get();
        return DataTables::of($bab)
            ->addColumn('aksi', function($aksi) {
                return '<div class="btn-group">
                <button type="button" class="btn btn-warning" onclick="editData('.$aksi->id.', this)">
                <i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-info" onclick="detailData('.$aksi->id.')">
                <i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-danger" onclick="deleteData('.$aksi->id.', this)">
                <i class="fa fa-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function sort($id, Request $request)
    {
        $kata = $request->post('kata');
        $babId = $request->post('babId');
        $postId = $request->post('postId');
        $baitId = $request->post('baitId');

        $conditions = [
            'post_id' => $postId,
            'bab_id' => $babId,
            'bait_id' => $baitId,
        ];

        $originalOrder = PostKata::where($conditions)->orderBy('no', 'ASC')->pluck('id')->toArray();
        foreach($kata as $key => $value){
            if($originalOrder[$key] !== $value){
                PostKata::where('id', $value)->update(['no' => $key+1]);
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'sukses'
        ]);
    }
}
