<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

final class Index extends Controller
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
    public function __invoke(Request $request)
    {
        $input = $request->json()->all();
        
        return response()->json([
            'status' => 'Ok',
            'data' => $this->getHandler($input),
            'message' => 'Item has been processed.',
        ]);
    }

    private function getHandler($input)
    {
        if (
            array_key_exists('select', $input) && 
            is_array($input['select']) && 
            count($input['select']) > 0
        ) {

            if (in_array('products', $input['select'])) {
                return $this->selectWithProduct($input);
            }

            return Category::select($this->selectHandler($input))
                ->limit($this->limitHandler($input))
                ->offset($this->offsetHandler($input))
                ->get();

        } else {

            return Category::with('products.product')
                ->limit($this->limitHandler($input))
                ->offset($this->offsetHandler($input))
                ->get();

        }
    }

    private function selectWithProduct($input)
    {
        $data = DB::table('categories')
        ->limit($this->limitHandler($input))
        ->offset($this->offsetHandler($input))
        ->get();

        foreach ($data as $key => $value) {
            
            $data[$key]->products = DB::table('products')
                                    ->select('products.*')
                                    ->join(
                                        'product_categories', 
                                        'products.id', 
                                        '=', 
                                        'product_categories.product_id'
                                    )->where('product_categories.category_id', '=', $value->id)
                                    ->get();


            foreach ($value as $key2 => $value2) {

                if (in_array($key2, $input['select'])) {
                    continue;
                }

                unset($data[$key]->{$key2});
            }

        }

        return $data;
    }

    private function selectHandler($input)
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

    private function limitHandler($input)
    {
        if (!array_key_exists('limit', $input) || intval($input['limit']) > 100) {
            return 100;
        }

        return intval($input['limit']);
    }

    private function offsetHandler($input)
    {
        if (!array_key_exists('start', $input) || intval($input['start']) > 100) {
            return 0;
        }

        return intval($input['start']);
    }

}
