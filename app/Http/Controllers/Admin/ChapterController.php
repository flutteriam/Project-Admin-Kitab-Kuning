<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use App\Models\PostBab;
use App\Models\Category;
use App\Models\PostBait;
use App\Models\PostKata;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\ChapterCreateRequest;
use App\Http\Requests\ChapterUpdateRequest;

class ChapterController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($id = null)
    {
        $categories = Category::all();
        $bab = null;
        $temp_post = null;
        $post = null;
        $first_bait = null;
        $active_category = null;
        if($id) {
            $bab = PostBab::with('post')->find($id);
            $temp_post = Post::find($bab->post_id);
            $post = Post::where('category_id', $temp_post->category_id)->get();
            $active_category = Category::find($post->first()->category_id);
            $first_bait = PostBait::where('bab_id', $bab->id)->orderBy('no', 'ASC')->first()->id;
        }
        return view('post::chapter', compact('categories', 'bab', 'temp_post', 'post', 'active_category', 'first_bait'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ChapterCreateRequest $request)
    {
        $last_no = PostKata::where('bab_id', $request->bab_id)->where('bait_id', $request->bait_id)->latest()->first()->no ?? 0;
        $request->merge([
            'no' => ($last_no + 1)
        ]);
        PostKata::create($request->all());
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
        // Id => id bait bukan id kata
        return response()->json([
            'status' => true,
            'data' => PostKata::where('bait_id', $id)->orderBy('no', 'ASC')->get()
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $chapter = PostKata::with('post')->find($id);
        return response()->json([
            'status' => true,
            'data' => $chapter
        ],200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(ChapterUpdateRequest $request, $id)
    {
        $chapter = PostKata::find($id);
        $chapter->update($request->all());
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
        $kata = PostKata::find($id);
        $lower_kata = PostKata::where('no', '>', $kata->no)->get();
        foreach ($lower_kata as $key => $value) {
            $temp_no = $value->no;
            $value->update([
                'no' => ($temp_no - 1)
            ]);
        }
        $kata->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Kata'
            ]
        ], 200);
    }

    public function update_number($id, $type) {
        $kata = PostKata::find($id);
        $current_number = $kata->no;
        // 1 Untuk naik, 2 untuk turun (kolom no)
        if($kata->no == 1 && $type == 1) {
            return $this->response_message('Error', 'Kata tersebut sudah paling awal');
        } else {
            // Jika Naik
            if($type == 1) {
                $atasnya = $current_number - 1;
                $temp_kata = PostKata::where('bait_id', $kata->bait_id)->where('no', $atasnya)->first();
                $kata->update([
                    'no' => $atasnya
                ]);
                $temp_kata->update([
                    'no' => $current_number
                ]);
            }
            // Jika Turun
            if($type == 2) {
                $bawahnya = $current_number + 1;
                $check_last = PostKata::where('bait_id', $kata->bait_id)->where('no', $bawahnya)->first();
                // Jika Sudah Nomer Terkahir maka tidak bisa turun lagi boss
                if(!$check_last) {
                    return $this->response_message('Error', 'Kata tersebut sudah paling akhir');
                } else {
                    $kata->update([
                        'no' => $bawahnya
                    ]);
                    $check_last->update([
                        'no' => $current_number
                    ]);
                }
            }
            return $this->response_message('Sukses', 'Update Urutan Kata', 200);
        }
    }

    public function duplicate(Request $request)
    {
        $id = $request->id;
        $row = PostKata::find($id)->toArray(); //findone
        $last = PostKata::where([
            "post_id" => $row['post_id'],
            "bab_id" => $row['bab_id'],
            "bait_id" => $row['bait_id']
        ])->orderBy('no', 'DESC')->first();
        $row['no'] = $last->no+1;


        PostKata::create($row);

        return $this->response_message('Sukses', 'Duplikat Kata', 200);
    }

    public function datatable(Request $request) {
        $bab_id = $request->bab;
        $bait_id = $request->bait;
        $kata = PostKata::with('bait')
                    ->where('bab_id', $bab_id)
                    ->where('bait_id', $bait_id)
                    ->orderBy('no', 'ASC')->get();
        return DataTables::of($kata)
            ->addColumn('aksi', function($aksi) {
                return '<div class="btn-group">
                            <button type="button" class="btn btn-warning" onclick="editData('.$aksi->id.', this)"> <i class="fa fa-pencil"></i></button>
                            <button type="button" class="btn btn-info" onclick="setDir('.$aksi->id.', 1, this)"> <i class="fa fa-arrow-up"></i></button>
                            <button type="button" class="btn btn-info" onclick="setDir('.$aksi->id.', 2, this)"> <i class="fa fa-arrow-down"></i></button>
                            <button type="button" class="btn btn-danger" onclick="deleteData('.$aksi->id.', this)"> <i class="fa fa-trash"></i></button>
                        </div>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    private function response_message($head, $body, $code = 500) {
        return response()->json([
            'status' => false,
            'message' => [
                'head' => $head,
                'body' => $body
            ]
        ], $code);
    }
}
