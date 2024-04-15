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
        if ($id) {
            $bab = Bab::with('book')->find($id);
            $temp_book = Book::find($bab->book_id);
            $book = Book::where('category_id', $temp_book->category_id)->get();
            $active_category = Category::find($book->first()->category_id);
        }
        return view('book::chapter', compact('categories', 'bab', 'temp_book', 'book', 'active_category'));
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
            'order' => $order,
            'bab_id' => $request->bab_id,
            'book_id' => $request->book_id,
            'translate' => $request->translate,
            'description' => $request->description,
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
        $chapter = Chapter::with(['book', 'bab'])->find($id);
        $category = $chapter->first()->book->category->id;
        return response()->json([
            'status' => true,
            'data' => [
                'chapter' => $chapter,
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
        $chapter = Chapter::find($id);
        try {
            $old = Chapter::where([
                'order' => $request->order,
                'bab_id' => $chapter->bab_id,
            ])->first();
            $old->update(['order' => $chapter->order]);
        } catch (\Throwable $th) {
            //throw $th;
        }
        $chapter->update([
            'order' => $request->order,
            'translate' => $request->translate,
            'description' => $request->description,
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah chapter'
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
        $chapter = Chapter::find($id);
        $nextChapter = Chapter::where([
            ['bab_id', $chapter->bab_id],
            ['order', '>', $chapter->order]
        ])->get();
        foreach ($nextChapter as $chap) {
            $chap->update(['order' => $chap->order - 1]);
        }
        $chapter->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Chapter'
            ]
        ], 200);
    }
}
