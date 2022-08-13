<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Factura extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'emisor',
        'comprador',
        'valor_antes_iva',
        'iva',
        'valor_a_pagar',
        'items_facturados'

    ];

    public static function getFactura($limit, $offset, $order, $dir){
        return DB::table('facturas')->select(DB::raw("id, created_at as fecha, emisor,  comprador, valor_antes_iva, iva, valor_a_pagar, items_facturados, '' AS opciones"))
        ->orderBy($order, $dir)
        ->skip($offset)->take($limit)->get();
    }


}
