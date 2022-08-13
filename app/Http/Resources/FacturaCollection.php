<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FacturaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {

        return [
            // "draw"            => intval(1),
            "recordsTotal"    => intval(1),
            "recordsFiltered" => intval(1),
            'data' => $this->collection,
            'meta' => [
                'author' => 'Daniela MC'
            ],
            'type' => 'Factura'
        ];
    }
}
