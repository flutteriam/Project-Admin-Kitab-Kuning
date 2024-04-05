<?php

namespace App\Http\Controllers\Admin;

use App\Models\PostBab;
use App\Models\Category;
use App\Models\Post;
use App\Models\PostBait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Http\Requests\BaitCreateRequest;
use Http\Requests\BaitUpdateRequest;

class BaitController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request, $id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_post = null;
        $post = null;
        $active_category = null;
        if($id) {
            $bab = PostBab::with('post')->find($id);
            $temp_post = Post::find($bab->post_id);
            $post = Post::where('category_id', $temp_post->category_id)->get();
            $active_category = Category::find($post->first()->category_id);
        }
        return view('post::bait', compact('categories', 'bab', 'temp_post', 'post', 'active_category'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BaitCreateRequest $request)
    {
        $last_no = PostBait::where('bab_id', $request->bab_id)->latest()->first()->no ?? 0;
        $request->merge([
            'no' => ($last_no + 1)
        ]);
        PostBait::create($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Bait baru'
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
            'data' => PostBait::with('kata')->where('bab_id', $id)->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $bait = PostBait::with(['post', 'bab'])->find($id);
        $category = $bait->first()->post->category->id;
        return response()->json([
            'status' => true,
            'data' => [
                'bait' => $bait,
                'category' => $category
            ]
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BaitUpdateRequest $request, $id)
    {
        $bait = PostBait::find($id);
        $bait->update($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah bait'
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
        $bait = PostBait::find($id);
        $lower_bait = PostBait::where('no', '>', $bait->no)->get();
        foreach ($lower_bait as $key => $value) {
            $temp_no = $value->no;
            $value->update([
                'no' => ($temp_no - 1)
            ]);
        }
        $bait->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Bait'
            ]
        ], 200);
    }

    public function datatable(Request $request) {
        $bab_id = $request->bab;
        $post_id = $request->post;
        $bab = PostBait::where('bab_id', $bab_id)->where('post_id', $post_id)->with(['post', 'bab', 'kata'])->orderBy('no', 'ASC')->get();
        // $bab = PostBait::when($bab_id, function($q) use($bab_id) {
        //     $q->where('bab_id', $bab_id);
        // })->when($post_id, function($q) use($post_id) {
        //     $q->where('post_id', $post_id);
        // })->with(['post', 'bab', 'kata'])->get();
        return DataTables::of($bab)
            ->addColumn('aksi', function($aksi) use($bab_id) {
                return '<div class="btn-group">
                <button type="button" class="btn btn-warning" onclick="editData('.$aksi->id.', this)">
                <i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-info" onclick="detailData('.$bab_id.')">
                <i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-danger" onclick="deleteData('.$aksi->id.', this)">
                <i class="fa fa-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
