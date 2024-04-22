<?php

namespace App\Http\Controllers\api\v1;

use App\Models\BookLike;
use App\Models\SavedBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SavedBookController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'uid' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $data = SavedBook::create($request->all());
        if (is_null($data)) {
            $response = [
                'data' => $data,
                'message' => 'error',
                'status' => 500,
            ];
            return response()->json($response, 200);
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getById(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $data = SavedBook::find($request->id);

        if (is_null($data)) {
            $response = [
                'success' => false,
                'message' => 'Data not found.',
                'status' => 404
            ];
            return response()->json($response, 404);
        }

        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = SavedBook::find($request->id)->update($request->all());

        if (is_null($data)) {
            $response = [
                'success' => false,
                'message' => 'Data not found.',
                'status' => 404
            ];
            return response()->json($response, 404);
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
            'book_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = SavedBook::where(['book_id' => $request->book_id, 'uid' => $request->uid])->first();
        if ($data) {
            $data->delete();
            $response = [
                'data' => $data,
                'success' => true,
                'status' => 200,
            ];
            return response()->json($response, 200);
        }
        $response = [
            'success' => false,
            'message' => 'Data not found.',
            'status' => 404
        ];
        return response()->json($response, 404);
    }

    public function getAll()
    {
        $data = SavedBook::all();
        if (is_null($data)) {
            $response = [
                'success' => false,
                'message' => 'Data not found.',
                'status' => 404
            ];
            return response()->json($response, 404);
        }

        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getSavedBook(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = DB::table('saved_books')
            ->select(
                'saved_books.id as id',
                'books.id as book_id',
                'books.category_id as category_id',
                'books.comments as comments',
                'books.content as content',
                'books.cover as cover',
                'books.created_at',
                'books.likes as likes',
                'books.description as description',
                'books.slugs as slugs',
                'books.status as status',
                'books.title as title',
                'books.type as type',
                'categories.name as cate_name'
            )
            ->join('books', 'saved_books.book_id', 'books.id')
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->orderBy('books.id', 'desc')
            ->where('saved_books.uid', $request->uid)
            ->get();
        foreach ($data as $loop) {
            $loop->uid = $request->uid;
            $temp = BookLike::where(['uid' => $request->uid, 'book_id' => $loop->book_id])->first();
            if (isset($temp) && $temp->id) {
                $loop->haveLiked = true;
            }
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
}
