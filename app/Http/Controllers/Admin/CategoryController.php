<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use Http\Requests\Admin\CategoryCreateRequest;
use Http\Requests\Admin\CategoryUpdateRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('admin.category');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(CategoryCreateRequest $request)
    {
        Category::create(['name' => $request->name]);
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
        $category->update([
            'name' => $request->name
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
        $category->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Kategori'
            ]
        ], 200);
    }

    public function datatable() {
        $category = Category::all();
        return DataTables::of($category)
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
