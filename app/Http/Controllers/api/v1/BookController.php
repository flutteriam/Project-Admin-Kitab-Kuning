<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Book;
use App\Models\User;
use App\Models\BookLike;
use App\Models\SavedBook;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function uploadVideo(Request $request)
    {
        ini_set('upload_max_filesize', '500M');
        ini_set('post_max_size', '500M');
        Artisan::call('storage:link', []);
        $uploadFolder = 'video';
        $image = $request->file('video');
        $image_uploaded_path = $image->store($uploadFolder, 'public');
        $uploadedImageResponse = array(
            "image_name" => basename($image_uploaded_path),
            "mime" => $image->getClientMimeType()
        );
        $response = [
            'data' => $uploadedImageResponse,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'title' => 'required',
            'slugs' => 'required',
            'cover' => 'required',
            'type' => 'required',
            'content' => 'required',
            'description' => 'required',
            'likes' => 'required',
            'comments' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $slug = Book::where('slugs', $request->slugs)->first();
        if (isset($slug)) {
            $response = [
                'success' => $slug,
                'message' => 'URL slug already taken',
                'status' => 500
            ];
            return response()->json($response, 500);
        }
        $data = Book::create($request->all());
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

        $data = Book::find($request->id);

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
        $slug = Book::where([['slugs', '=', $request->slugs], ['id', '!=', $request->id]])->first();
        if (isset($slug)) {
            $response = [
                'success' => $slug,
                'message' => 'URL slug already taken',
                'status' => 500
            ];
            return response()->json($response, 500);
        }
        $data = Book::find($request->id)->update($request->all());

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
        $data = Book::find($request->id);
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

    public function getAll(Request $request)
    {
        $data = Book::orderBy('id', 'desc')->get();
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

    public function getHomeData(Request $request)
    {

        $data = DB::table('books')
            ->select(
                'books.id as id',
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
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->orderBy('books.id', 'desc')
            ->limit(10)
            ->get();
        $categories = Category::count();
        $books = Book::count();
        $users = User::where('type', 1)->count();
        $response = [
            'data' => $data,
            'categories' => $categories,
            'books' => $books,
            'users' => $users,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getByCate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
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
        $matchThese = ['books.status' => 1, 'books.category_id' => $request->id];
        $data = DB::table('books')
            ->select(
                'books.id as id',
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
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->where($matchThese)
            ->orderBy('books.id', 'desc')
            ->limit($request->limit)
            ->get();
        foreach ($data as $loop) {
            if ($request->uid) {
                $temp = BookLike::where(['uid' => $request->uid, 'book_id' => $loop->id])->first();
                $tempSaved = SavedBook::where(['uid' => $request->uid, 'book_id' => $loop->id])->first();
                if (isset($temp) && $temp->id) {
                    $loop->haveLiked = true;
                } else {
                    $loop->haveLiked = false;
                }

                if (isset($tempSaved) && $tempSaved->id) {
                    $loop->haveSaved = true;
                } else {
                    $loop->haveSaved = false;
                }
            } else {
                $loop->haveLiked = false;
                $loop->haveSaved = false;
            }
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getRelate(Request $request)
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
        $matchThese = ['books.status' => 1, 'books.category_id' => $request->id];
        $data = DB::table('books')
            ->select(
                'books.id as id',
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
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->where($matchThese)
            ->orderBy('books.id', 'desc')
            ->limit(10)
            ->get();
        foreach ($data as $loop) {
            if ($request->uid) {
                $temp = BookLike::where(['uid' => $request->uid, 'book_id' => $loop->id])->first();
                $tempSaved = SavedBook::where(['uid' => $request->uid, 'book_id' => $loop->id])->first();
                if (isset($temp) && $temp->id) {
                    $loop->haveLiked = true;
                } else {
                    $loop->haveLiked = false;
                }

                if (isset($tempSaved) && $tempSaved->id) {
                    $loop->haveSaved = true;
                } else {
                    $loop->haveSaved = false;
                }
            } else {
                $loop->haveLiked = false;
                $loop->haveSaved = false;
            }
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getVideoBook(Request $request)
    {
        $matchThese = ['books.status' => 1, 'books.type' => 2];
        $data = DB::table('books')
            ->select(
                'books.id as id',
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
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->where($matchThese)
            ->orderBy('books.id', 'desc')
            ->get();

        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function getBySlugs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug'    => 'required'
        ]);
        if ($validator->fails()) {
            $response = [
                'success' => false,
                'message' => 'Validation Error.', $validator->errors(),
                'status' => 500
            ];
            return response()->json($response, 404);
        }
        $matchThese = ['books.slugs' => $request->slug];
        $data = DB::table('books')
            ->select(
                'books.id as id',
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
                'categories.name as cate_name',
                'categories.title_color as title_color'
            )
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->where($matchThese)
            ->first();

        $response = [
            'data' => $data,
            'success' => true,
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
        $matchThese = ['books.id' => $request->id];
        $data = DB::table('books')
            ->select(
                'books.id as id',
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
            ->join('categories', 'books.category_id', '=', 'categories.id')
            ->where($matchThese)
            ->first();
        if ($request->uid) {
            $temp = BookLike::where(['uid' => $request->uid, 'book_id' => $data->id])->first();
            $tempSaved = SavedBook::where(['uid' => $request->uid, 'book_id' => $data->id])->first();
            if (isset($temp) && $temp->id) {
                $data->haveLiked = true;
            } else {
                $data->haveLiked = false;
            }

            if (isset($tempSaved) && $tempSaved->id) {
                $data->haveSaved = true;
            } else {
                $data->haveSaved = false;
            }
        } else {
            $data->haveLiked = false;
            $data->haveSaved = false;
        }
        $response = [
            'data' => $data,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }

    public function searchQuery(Request $request)
    {
        $str = "";
        if ($request->has('param')) {
            $str = $request->param;
        }

        $books = Book::select('id', 'title', 'cover', 'slugs')->where('status', 1)->where('title', 'like', '%' . $str . '%')->orderBy('id', 'asc')->limit(5)->get();

        $response = [
            'books' => $books,
            'success' => true,
            'status' => 200,
        ];
        return response()->json($response, 200);
    }
}
