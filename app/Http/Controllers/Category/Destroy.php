<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;

final class Destroy extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {

        $data = Category::find($id);

        if (empty($data)) {

            return response()->json([
                'status' => 'Page Not Found',
                'message' => 'Route not found for this item.'
            ], 404);

        }

        return response()->json([
            'status' => 'Ok',
            'data' => $data->delete(),
            'message' => 'Item has been deleted.'
        ]);

    }
}