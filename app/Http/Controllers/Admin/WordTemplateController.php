<?php

namespace App\Http\Controllers\Admin;

use App\Models\WordTemplate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Requests\WordTemplateCreateRequest;
use App\Http\Requests\WordTemplateUpdateRequest;

class WordTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('post::word_template');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(WordTemplateCreateRequest $request)
    {
        WordTemplate::create($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menambahkan template kata baru'
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
            'data' => WordTemplate::find($id)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(WordTemplateUpdateRequest $request, $id)
    {
        $word_template = WordTemplate::find($id);
        $word_template->update($request->all());
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Mengubah Template Kata'
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
        $word_template = WordTemplate::find($id);
        $word_template->delete();
        return response()->json([
            'status' => true,
            'message' => [
                'head' => 'Berhasil',
                'body' => 'Menghapus Template Kata'
            ]
        ], 200);
    }

    public function show($id = null) {
        $template_word = WordTemplate::when($id, function($q) use ($id) {
            $q->where('id', $id);
        })->get();
        return response()->json([
            'status' => true,
            'data' => $template_word
        ], 200);
    }

    public function datatable(Request $request) {
        $word_template = WordTemplate::all();
        return DataTables::of($word_template)
            ->addColumn('aksi', function($aksi) {
                return '<div class="btn-group">
                <button type="button" class="btn btn-warning" onclick="editData('.$aksi->id.', this)">
                <i class="fa fa-pencil"></i></button>
                <button type="button" class="btn btn-danger" onclick="deleteData('.$aksi->id.', this)">
                <i class="fa fa-trash"></i></button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
