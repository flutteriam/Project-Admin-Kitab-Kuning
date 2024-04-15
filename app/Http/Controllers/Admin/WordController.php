<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\WordCreateRequest;
use App\Http\Requests\Admin\WordUpdateRequest;
use App\Models\Book;
use App\Models\Bab;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_book = null;
        $book = null;
        $first_chapter = null;
        $active_category = null;
        if ($id) {
            $bab = Bab::with('book')->find($id);
            $temp_book = Book::find($bab->book_id);
            $book = Book::where('category_id', $temp_book->category_id)->get();
            $active_category = Category::find($book->first()->category_id);
            $first_chapter = Chapter::where('bab_id', $bab->id)->orderBy('order', 'ASC')->first()->id;
        }
        return view('book::chapter', compact('categories', 'bab', 'temp_book', 'book', 'active_category', 'first_chapter'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(WordCreateRequest $request)
    {
        $order = Word::where('chapter_id', $request->chapter_id)->count() + 1;
        $request->merge([
            'order' => $order
        ]);
        Word::create($request->except(['_token', 'category_id']));
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan Kata baru'
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
        // Id => id chapter bukan id kata
        return response()->json([
            'status' => true,
            'data' => Word::where('chapter_id', $id)->orderBy('order', 'ASC')->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $word = Word::with('book')->find($id);
        return response()->json([
            'status' => true,
            'data' => $word
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(WordUpdateRequest $request, $id)
    {
        $chapter = Word::find($id);
        $chapter->update([
            'arab' => $request->arab,
            'arab_harokat' => $request->arab_harokat,
            'translate' => $request->translate,
            'basic' => $request->basic,
        ]);
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah kata'
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
        $word = Word::find($id);
        $nextWord = Word::where([
            ['chapter_id', $word->chapter_id],
            ['order', '>', $word->order]
        ])->get();
        foreach ($nextWord as $w) {
            $w->update(['order' => $w->order - 1]);
        }
        $word->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Kata'
            ]
        ], 200);
    }

    public function update_number($id, $type)
    {
        $kata = Word::find($id);
        $current_number = $kata->order;
        // 1 Untuk naik, 2 untuk turun (kolom no)
        if ($kata->order == 1 && $type == 1) {
            return $this->response_message('Error', 'Kata tersebut sudah paling awal');
        } else {
            // Jika Naik
            if ($type == 1) {
                $atasnya = $current_number - 1;
                $temp_kata = Word::where('chapter_id', $kata->chapter_id)->where('order', $atasnya)->first();
                $kata->update([
                    'order' => $atasnya
                ]);
                $temp_kata->update([
                    'order' => $current_number
                ]);
            }
            // Jika Turun
            if ($type == 2) {
                $bawahnya = $current_number + 1;
                $check_last = Word::where('chapter_id', $kata->chapter_id)->where('order', $bawahnya)->first();
                // Jika Sudah Nomer Terkahir maka tidak bisa turun lagi boss
                if (!$check_last) {
                    return $this->response_message('Error', 'Kata tersebut sudah paling akhir');
                } else {
                    $kata->update([
                        'order' => $bawahnya
                    ]);
                    $check_last->update([
                        'order' => $current_number
                    ]);
                }
            }
            return $this->response_message('Sukses', 'Update Urutan Kata', 200);
        }
    }

    public function duplicate(Request $request)
    {
        $id = $request->id;
        $row = Word::find($id)->toArray();
        $count = Word::where([
            "chapter_id" => $row['chapter_id']
        ])->count() + 1;
        $row['order'] = $count;

        unset($row['id']);
        unset($row['created_at']);
        unset($row['updated_at']);
        Word::create($row);

        return $this->response_message('Sukses', 'Duplikat Kata', 200);
    }

    public function datatable(Request $request)
    {
        $bab_id = $request->bab;
        $chapter_id = $request->chapter;
        $kata = Word::with('chapter')
            ->where('bab_id', $bab_id)
            ->where('chapter_id', $chapter_id)
            ->orderBy('order', 'ASC')->get();
        return DataTables::of($kata)
            ->addColumn('aksi', function ($aksi) {
                return '<div class="btn-group">
                            <button type="button" class="btn btn-warning" onclick="editData(' . $aksi->id . ', this)"> <i class="fa fa-pencil"></i></button>
                            <button type="button" class="btn btn-info" onclick="setDir(' . $aksi->id . ', 1, this)"> <i class="fa fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-info" onclick="setDir(' . $aksi->id . ', 2, this)"> <i class="fa fa-arrow-down"></i></button>
                            <button type="button" class="btn btn-danger" onclick="deleteData(' . $aksi->id . ', this)"> <i class="fa fa-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    private function response_message($head, $body, $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => [
                'head' => $head,
                'body' => $body
            ]
        ], $code);
    }
}
