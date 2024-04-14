<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ChapterCreateRequest;
use App\Http\Requests\Admin\ChapterUpdateRequest;
use App\Models\Bab;
use App\Models\Book;
use App\Models\Category;
use App\Models\Chapter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request, $id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_book = null;
        $book = null;
        $active_category = null;
        if($id) {
            $bab = Bab::with('book')->find($id);
            $temp_book = Book::find($bab->book_id);
            $book = Book::where('category_id', $temp_book->category_id)->get();
            $active_category = Category::find($book->first()->category_id);
        }
        return view('book::bait', compact('categories', 'bab', 'temp_book', 'book', 'active_category'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ChapterCreateRequest $request)
    {
        $order = Chapter::where('bab_id', $request->bab_id)->count() + 1;
        Chapter::create([
            'order'=> $order,
            'bab_id'=> $request->bab_id,
            'book_id'=> $request->book_id,
            'translate'=> $request->translate,
            'description'=> $request->description,
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Chapter baru'
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
            'data' => Chapter::with('words')->where('bab_id', $id)->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $bait = Chapter::with(['book', 'bab'])->find($id);
        $category = $bait->first()->book->category->id;
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
    public function update(ChapterUpdateRequest $request, $id)
    {
        $bait = Chapter::find($id);
        $bait->update([
            'translate'=> $request->translate,
            'description'=> $request->description,
        ]);
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
        $bait = Chapter::find($id);
        $nextChapter = Chapter::where('order', '>', $bait->order)->get();
        foreach ($nextChapter as $chapter) {
            $chapter->update(['order' => $chapter->order - 1]);
        }
        $bait->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Chapter'
            ]
        ], 200);
    }

    public function datatable(Request $request) {
        $bab_id = $request->bab;
        $book_id = $request->book;
        $bab = Chapter::where('bab_id', $bab_id)->where('book_id', $book_id)->with(['book', 'bab', 'words'])->orderBy('order', 'ASC')->get();
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
