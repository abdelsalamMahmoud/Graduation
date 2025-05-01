<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


trait PaginationListTrait{

    public function PaginationList(
        Request $request,
        string $model,
        string $type,
        array $select,
        array $with,
        callable $mapCallback
    ){
    try {
        $perPage = max(1, min($request->input('per_page', 10), 100));

        $query = $model::select($select)->with($with);

        if (method_exists($this, 'applyConditions')) {
            $query = $this->applyConditions($query, $type, $request);
        }


        if (!$query) {
            return $this->apiResponse([
                'data' => [
                    $type => [],
                    'pagination' => [
                        'total' => 0,
                        'per_page' => $perPage,
                        'current_page' => 1,
                        'last_page' => 1
                    ]
                ]
            ], "No data found in $type", 200);
        }

        $items = $query->paginate($perPage);

        if ($items->total() == 0) {
            return $this->apiResponse(null, "Not $type Found", 200);
        }

        $data = $items->map($mapCallback);

        $responseData = [
            $type => $data,
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ],
        ];


        return $this->apiResponse($responseData, "$type retrieved successfully", 200);
    }
    catch (\Exception $e) {
        return $this->apiResponse(null, "An error occurred while retrieving $type", 500 ,);
    }
    }


public function applyConditions($query, $type, $request)
    {
        return $query; 
    }
}