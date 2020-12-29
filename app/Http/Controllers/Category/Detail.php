<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

final class Detail extends Controller
{
    const COLUMN_IDENTIFIED = [
        'id',
        'name',
        'slug',
        'description',
        'created_at',
        'updated_at',
        'products'
    ];

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id, Request $request)
    {
        $input = $request->json()->all();
        
        return response()->json([
            'status' => 'Ok',
            'data' => $this->getHandler($input, $id),
            'message' => 'Item has been processed.',
        ]);
    }

    private function getHandler($input, $id)
    {
        if (
            array_key_exists('select', $input) && 
            is_array($input['select']) && 
            count($input['select']) > 0
        ) {

            if (in_array('products', $input['select'])) {
                return $this->selectWithProduct($input, $id);
            }

            return Category::select($this->selectHandler($input, $id))
                ->find($id);

        } else {

            return Category::with('products.product')
                ->find($id);

        }
    }

    private function selectWithProduct($input, $id)
    {
        $data = DB::table('categories')
                    ->where('id', '=', $id)
                    ->first();

        if (empty($data)) {
            return null;
        }

        $data->products = DB::table('products')
                                ->select('products.*')
                                ->join(
                                    'product_categories', 
                                    'products.id', 
                                    '=', 
                                    'product_categories.product_id'
                                )->where('product_categories.category_id', '=', $id)
                                ->get();

        foreach ($data as $key => $value) {
            
            foreach ($value as $key2 => $value2) {

                if (in_array($key2, $input['select'])) {
                    continue;
                }

                unset($data->{$key2});
            }

        }

        return $data;
    }

    private function selectHandler($input, $id)
    {

        $selected = [];

        foreach ($input['select'] as $key => $value) {

            if (!in_array($value, self::COLUMN_IDENTIFIED)) {
                continue;
            }

            $selected[] = $value;
        }

        
        if (empty($selected)) {
            return self::COLUMN_IDENTIFIED;
        }

        return $selected;
    }

}
