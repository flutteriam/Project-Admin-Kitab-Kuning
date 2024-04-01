<?php

namespace App\Http\Controllers\api\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bab;
use Illuminate\Support\Facades\Validator;

class BabController extends Controller
{
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required',
            'order' => 'required',
            'title' => 'required',
            'translate_title' => 'nullable'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $data = Bab::create($request->all());
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

        $data = Bab::find($request->id);

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
        $data = Bab::find($request->id)->update($request->all());

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
        $data = Bab::find($request->id);
        if ($data) {
            $nextBab = Bab::where('order', '>', $data->order)->get();
            foreach ($nextBab as $bab) {
                $bab->update(['order' => $bab->order - 1]);
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
        $data = Bab::orderBy('id', 'desc')->get();
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

        $old = Bab::where('order', $request->order)->first();
        $current = Bab::where('id', $request->id)->first();
        $updateNew = Bab::where('id', $old->id)->update(['order' => $current->order]);
        $data = Bab::find($request->id)->update($request->all());

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

    public function getByBookId(Request $request)
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
        $data = Bab::whereBookId($request->id)->get();
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
}
