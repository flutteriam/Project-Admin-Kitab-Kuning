<?php
namespace App\Http\Controllers\api\v1\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
    }
    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Invalidate current logged user token
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out','status'=>200], 200);
        // Return message
        // return response()
        //     ->json(['message' => 'Successfully logged out']);
    }
}
