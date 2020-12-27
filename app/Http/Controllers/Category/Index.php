<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

final class Index extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'status' => 'Ok',
            'data' => $this->getHandler($request)
        ]);
    }

    private function getHandler($request)
    {
        return Category::select($this->selectHandler($request))
                ->orderBy($this->orderHandler($request), $this->sortHandler($request))
                ->limit($this->limitHandler($request))
                ->offset($this->offsetHandler($request))
                ->get();
    }

    private function selectHandler($request)
    {
        $columns = $this->getColumns();

        if (empty($request->select)) {
            return $columns;
        }

        $selectRequest = explode(',', $request->select);

        if (count($selectRequest) > count($columns)) {
            return $columns;
        }

        $selected = [];

        foreach ($selectRequest as $key => $value) {

            if (!in_array($value, $columns)) {
                continue;
            }

            $selected[] = $value;
        }

        
        if (empty($selected)) {
            return $columns;
        }

        return $selected;
    }

    private function getColumns()
    {
        return [
            'id',
            'name',
            'slug',
            'description',
            'created_at',
            'updated_at'
        ];
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
        $columns = $this->getColumns();

        $order = 'id';

        if (empty($request->order) || !in_array($request->order, $columns)) {
            return $order;
        }

        return $request->order;
    }

    private function sortHandler($request)
    {
        $sort = 'asc';

        if (empty($request->sort) || ( $request->sort !== 'asc' && $request->sort !== 'desc' ) ) {
            return $sort;
        }

        return $request->sort;
    }

}
