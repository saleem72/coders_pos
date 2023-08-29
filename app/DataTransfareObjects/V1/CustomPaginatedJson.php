<?php

namespace App\DataTransfareObjects\V1;

class CustomPaginatedJson {
    public static function toArray($items,$data) {
        return [
            'status' => true,
            'message' => 'success',
            'data' =>  $data,
            'pagination' => [
                'total' => $items->total(),
                'perPage' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'lastPage' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'hasMore' => $items->hasMorePages(),
            ]
        ];
    }
}
