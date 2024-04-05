<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Http\Requests\PostCreateRequest;
use Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    private $path_image = 'uploads/posts';

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $active_cateogry = null;
        if($id) {
            $active_cateogry = Category::find($id);
        }
        return view('post::post', compact('categories', 'active_cateogry'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(PostCreateRequest $request)
    {
        if($request->hasFile('image_upload')) {
            $fileName = Str::slug($request->title).'-'.time().'.'.$request->image_upload->extension();
            $request->image_upload->move(public_path($this->path_image), $fileName);
        }
        $data = $request->all();
        $data['image'] = $fileName;
        Post::create($data);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Post baru'
            ]
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
            'data' => Post::with('category')->find($id)
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
            'data' => Post::where('category_id', $id)->get()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(PostUpdateRequest $request, $id)
    {
        $post = Post::find($id);
        if($request->hasFile('image_upload')) {
            $fileName = Str::slug($request->title).'-'.time().'.'.$request->image_upload->extension();
            $request->image_upload->move(public_path($this->path_image), $fileName);
        }
        $data = $request->all();
        $data['image'] = $fileName ?? $post->image;
        $post->update($data);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah Post'
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
        $post = Post::find($id);
        if($post->image) {
            // unlink(public_path(str_replace('/', '\\', $this->path_image)).'\\'.$post->image);
            unlink(public_path($this->path_image ."/".$post->image));
        }
        $post->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Post'
            ]
        ], 200);
    }

    public function datatable(Request $request) {
        $category = $request->category;
        $post = Post::with('category')->where('category_id', $category)->get();
        return DataTables::of($post)
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
}
