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

        if ($validation['status'] === 'Input Error') {
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
                'message' => $validator->errors()
            ];
        }

        return [
            'status' => true,
            'message' => $validator->errors()
        ];
    }

    /**
     * Get all validation rules for store or update data
     *
     * @return array
     */
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

        $number = 1;

        while ($this->countSlug($slug) > 0) {

            $number++;

            $slug = Str::slug("$name $number", '-');

        }

        return $slug;
    }

    private function countSlug($slug)
    {
        return Category::where('slug', '=', $slug)
                        ->count();
    }

    private function createHandler($input)
    {
        try {

            return response()->json([
                'status' => 'Ok',
                'data' => Category::create($input)
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'Server Error',
                'message' => 'Server is not ready.'
            ], 500);

        }
    }
}
