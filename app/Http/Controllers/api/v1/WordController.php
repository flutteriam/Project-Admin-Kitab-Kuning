<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Word;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WordController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'bab_id' => 'required',
            'chapter_id' => 'required',
            'order' => 'required',
            'arab' => 'required',
            'arab_harokat' => 'required',
            'translate' => 'required',
            'basic' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = Word::create($request->all());
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

        $data = Word::find($request->id);

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
        $data = Word::find($request->id)->update($request->all());

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
        $data = Word::find($request->id);
        if ($data) {
            $nextWord = Word::where('order', '>', $data->order)->get();
            foreach ($nextWord as $chapter) {
                $chapter->update(['order' => $chapter->order - 1]);
            }
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

    public function getAll(Request $request)
    {
        $data = Word::orderBy('id', 'desc')->get();
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


    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'order' => 'required',
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }

        $old = Word::where('order', $request->order)->first();
        $current = Word::where('id', $request->id)->first();
        $updateNew = Word::where('id', $old->id)->update(['order' => $current->order]);
        $data = Word::find($request->id)->update($request->all());

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
            'old' => $old,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function sort($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kata'    => 'required',
            'babId'    => 'required',
            'bookId'    => 'required',
            'chapterId'    => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $kata = $request->post('kata');
        $babId = $request->post('babId');
        $bookId = $request->post('bookId');
        $chapterId = $request->post('chapterId');

        $conditions = [
            'book_id' => $bookId,
            'bab_id' => $babId,
            'chapter_id' => $chapterId,
        ];

        $originalOrder = Word::where($conditions)->orderBy('order', 'ASC')->pluck('id')->toArray();
        foreach($kata as $key => $value){
            if($originalOrder[$key] !== $value){
                Word::where('id', $value)->update(['order' => $key+1]);
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'sukses'
        ]);
    }

    public function getByChapterId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'    => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = Word::whereChapterId($request->id)->get();
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
}
