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
        return response()->json([
            'status' => 'Ok',
            'data' => $this->getHandler($request, $id),
            'message' => 'Data has been processed.',
        ]);
    }

    private function getHandler($request, $id)
    {
        $selectedField = $this->selectHandler($request);

        if (empty($selectedField)) {

            return Category::with('products.product')
                ->find($id);

        } else {

            if (in_array('products', $selectedField)) {
                return $this->selectWithProduct($request, $selectedField, $id);
            }

            return Category::select($selectedField)
                ->find($id);
                
        }
    }

    private function selectWithProduct($request, $selectedField, $id)
    {
        $data = DB::table('categories')
        ->where('id', '=', $id)
        ->limit($this->limitHandler($request))
        ->offset($this->offsetHandler($request))
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
                                )->where(
                                    'product_categories.category_id', 
                                    '=', 
                                    $id
                                )->get();

        foreach ($data as $key => $value) {
            
            if (in_array($key, $selectedField)) {
                continue;
            }

            unset($data->{$key});

        }

        return $data;
    }

    private function selectHandler($request)
    {
        $columns = self::COLUMN_IDENTIFIED;

        if (empty($request->select)) {
            return null;
        }

        $selectRequest = explode(',', $request->select);

        if (count($selectRequest) > count($columns)) {
            return null;
        }

        $selected = [];

        foreach ($selectRequest as $key => $value) {

            if (!in_array($value, $columns)) {
                continue;
            }

            $selected[] = $value;
        }

        
        if (empty($selected)) {
            return null;
        }

        return $selected;
    }

    private function limitHandler($request)
    {
        if (empty($request->limit) || intval($request->limit) > 100) {
            return 100;
        }

        return intval($request->limit);
    }

    private function offsetHandler($request)
    {
        if (empty($request->start) || intval($request->start) > 100) {
            return 0;
        }

        return intval($request->start);
    }

    private function orderHandler($request)
    {
        $columns = self::COLUMN_IDENTIFIED;

        $order = 'id';

        if (empty($request->order_by) || !in_array($request->order_by, $columns)) {
            return $order;
        }

        return $request->order_by;
    }

    private function sortHandler($request)
    {
        $sort = 'asc';

        if (empty($request->sort_by) || ( $request->sort_by !== 'asc' && $request->sort_by !== 'desc' ) ) {
            return $sort;
        }

        return $request->sort_by;
    }

}
