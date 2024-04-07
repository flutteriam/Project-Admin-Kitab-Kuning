<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\Admin\BookCreateRequest;
use App\Http\Requests\Admin\BookUpdateRequest;

class BookController extends Controller
{
    private $path_image = 'uploads/posts';

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $active_category = null;
        if($id) {
            $active_category = Category::find($id);
        }
        return view('admin.book', compact('categories', 'active_category'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BookCreateRequest $request)
    {
        if ($request->hasFile('image_upload')) {
            $image = $request->file('image_upload');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the storage disk under the 'images' directory
            Storage::putFileAs($this->path_image, $image, $imageName);

            $image = $this->path_image . '/' . $imageName;
        }
        $data = $request->all();
        $data['cover'] = $image;
        Book::create($data);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Kitab baru'
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
            'data' => Book::with('category')->find($id)
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
            'data' => Book::where('category_id', $id)->get()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(BookUpdateRequest $request, $id)
    {
        $book = Book::find($id);
        if ($request->hasFile('image_upload')) {
            $image = $request->file('image_upload');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the storage disk under the 'images' directory
            Storage::putFileAs($this->path_image, $image, $imageName);

            $image = $this->path_image . '/' . $imageName;

            if (Storage::exists($book->cover)) {
                Storage::delete($book->cover);
                echo "File deleted successfully.";
            } else {
                echo "File does not exist.";
            }
        }
        $data = $request->all();
        $data['cover'] = $image ?? $book->cover;
        $book->update($data);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah Kitab'
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
        $book = Book::find($id);
        if (Storage::exists($book->cover)) {
            Storage::delete($book->cover);
            echo "File deleted successfully.";
        } else {
            echo "File does not exist.";
        }
        $book->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Kitab'
            ]
        ], 200);
    }

    public function datatable(Request $request) {
        $category = $request->category;
        $post = Book::with('category')->where('category_id', $category)->get();
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
