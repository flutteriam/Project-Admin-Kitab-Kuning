<?php

namespace App\Http\Controllers\Admin;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PdfController extends Controller
{
    public function download($id = null, Request $request)
    {
        // Mengambil data dari model berdasarkan ID
        $data = Book::find($id); // Ganti YourModel dengan model Anda

        if (!$data) {
            abort(404);
        }

        return view("book_pdf", compact("data"));
    }
}
