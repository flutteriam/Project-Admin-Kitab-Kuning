<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\BabCreateRequest;
use App\Http\Requests\Admin\BabUpdateRequest;
use App\Models\Bab;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use App\Models\Book;
use App\Models\Word;

class BabController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $selectedBook = null;
        $books = null;
        if ($id) {
            $selectedBook = Book::with('category')->find($id);
            $books = Book::where('category_id', $selectedBook->category_id)->get();
        }
        return view('admin.bab', compact("categories", "selectedBook", "books"));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(BabCreateRequest $request)
    {
        $order = Bab::where('book_id', $request->book_id)->count() + 1;
        Bab::create([
            'book_id' => $request->book_id,
            'order' => $order,
            'title' => $request->title,
            'translate_title'=> $request->translate_title,
        ]);
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
            'data' => Bab::where('book_id', $id)->get()
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
            'data' => Bab::with('book')->find($id)
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
        $bab = Bab::find($id);
        $bab->update([
            'title' => $request->title,
            'translate_title'=> $request->translate_title,
        ]);
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
        $bab = Bab::find($id);
        $nextBab = Bab::where([
            ['book_id', $bab->book_id],
            ['order', '>', $bab->order]
        ])->get();
        foreach ($nextBab as $bab) {
            $bab->update(['order' => $bab->order - 1]);
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

    public function ajax_post_detail($book_id)
    {
        $babs = Bab::where('book_id', $book_id)->with(['chapters.words' => function ($q) {
            $q->orderBy('order', 'ASC');
        }])->get();

        return view('admin.ajax_bab_detail', compact("babs"));
    }

    public function datatable(Request $request)
    {
        $book_id = $request->post;
        $bab = Bab::where('book_id', $book_id)->with(['book', 'chapters.words' => function ($q) {
            $q->orderBy('order', 'ASC');
        }])->get();

        return DataTables::of($bab)
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

    public function sort($id, Request $request)
    {
        $kata = $request->post('kata');
        $babId = $request->post('babId');
        $bookId = $request->post('bookId');
        $chapterId = $request->post('chapterId');

        $originalOrder = Word::where([
            'book_id' => $bookId,
            'bab_id' => $babId,
            'chapter_id' => $chapterId,
        ])->orderBy('order', 'ASC')->pluck('id')->toArray();

        foreach ($kata as $key => $value) {
            if ($originalOrder[$key] !== $value) {
                Word::where('id', $value)->update(['order' => $key + 1]);
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'sukses'
        ]);
    }
}
