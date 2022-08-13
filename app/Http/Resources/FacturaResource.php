<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTime;

class FacturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        if(isset($this->id)){
            return [
                "id" => $this->id,
                "fecha" => (new DateTime($this->fecha))->format("Y-m-d H:i:s"),
                "emisor" => json_decode($this->emisor),
                "comprador" => json_decode($this->comprador),
                "valor_antes_iva" => $this->valor_antes_iva,
                "iva" => $this->iva,
                "valor_a_pagar" => $this->valor_a_pagar,
                "items_facturados" => json_decode($this->items_facturados)
                

            ];
            
        }

        return [];
    }
}
