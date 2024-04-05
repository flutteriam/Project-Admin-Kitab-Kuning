<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\Admin\CategoryCreateRequest;
use App\Http\Requests\Admin\CategoryUpdateRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $count = Category::count();
        return view('admin.category', ['count' => $count]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CategoryCreateRequest $request)
    {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the storage disk under the 'images' directory
            Storage::putFileAs('categories', $image, $imageName);

            $image = 'categories/' . $imageName;
        }
        $slug = Str::slug($request->name);
        $count = Category::count();
        Category::create([
            'name' => $request->name,
            'slugs' => $slug,
            'order' => $count + 1,
            'cover' => $image ?? '',
            'status' => $request->status ?? 1,
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Kategori baru'
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
            'data' => Category::find($id)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(CategoryUpdateRequest $request, $id)
    {
        $category = Category::find($id);
        $slug = Str::slug($request->name);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Store the image in the storage disk under the 'images' directory
            Storage::putFileAs('categories', $image, $imageName);

            $image = 'categories/' . $imageName;

            if (Storage::exists($category->cover)) {
                Storage::delete($category->cover);
                echo "File deleted successfully.";
            } else {
                echo "File does not exist.";
            }
        }
        $old = Category::where('order', $request->order)->first();
        Category::where('id', $old->id)->update(['order' => $category->order]);
        $category->update([
            'name' => $request->name,
            'slugs' => $slug,
            'order' => $request->order,
            'cover' => $image ?? $category->cover,
            'status' => $request->status ?? 1,
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah Kategori'
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
        $category = Category::find($id);
        $nextCategory = Category::where('order', '>', $category->order)->get();
        foreach ($nextCategory as $cat) {
            $cat->update(['order' => $cat->order - 1]);
        }
        $category->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Kategori'
            ]
        ], 200);
    }

    public function datatable()
    {
        $category = Category::all();
        return DataTables::of($category)
            ->addColumn('aksi', function ($aksi) {
                return '<div class="btn-group">
                <button type="button" class="btn btn-warning" onclick="editData(' . $aksi->id . ', this)">
                <i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-info" onclick="detailData(' . $aksi->id . ')">
                <i class="fa fa-eye"></i></button>
                <button type="button" class="btn btn-danger" onclick="deleteData(' . $aksi->id . ', this)">
                <i class="fa fa-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
