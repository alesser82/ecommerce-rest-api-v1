<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Category;

final class Store extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $input = $request->all();

        $validation = $this->validation($input);

        if (is_array($validation)) {
            return response()->json($validation, 400);
        }

        $input['slug'] = $this->slugHandler($input['name']);

        return $this->createHandler($input);

    }

    private function validation($input)
    {
        $validator = Validator::make(
            $input,
            $this->getValidationRules()
        );

        if ($validator->fails()) {
            return [
                'status' => 'Input Error',
                'messages' => $validator->errors()
            ];
        }

        return true;
    }

    private function getValidationRules()
    {
        return [
            'name' => [
                'required', 'string', 'max:200'
            ],
            'descryption' => [
                'nullable', 'string', 'max:60000'
            ]
        ];
    }

    private function slugHandler($name)
    {
        $slug = Str::slug($name, '-');

        $countedData = Category::where('slug', '=', $slug)
                        ->count();

        if ($countedData < 1) {
            return $slug;
        }

        $countedData++;

        return Str::slug("$name $countedData", '-');
    }

    private function createHandler($input)
    {
        try {
            
            Category::create($input);

            return response()->json([
                'status' => 'Ok'
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'Server Error',
                'message' => 'Server is not ready.'
            ], 500);

        }
    }
}
