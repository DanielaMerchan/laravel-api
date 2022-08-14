<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FacturaResource;
use App\Http\Resources\FacturaCollection;
use App\Models\Factura;
use App\Repositories\FacturaRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class FacturaController extends Controller
{
    /**
    * Se construye el CRUD de factura
    * En caso de no tener algún método preliminar se retonrnará desde el index la lista de facturas que existan
    **/

    /** 
     * @var FacturaRepositoryInterface 
     * */
    private $repository;

    public function __construct(FacturaRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'fecha',
            2 => 'emisor',
            3 => 'comprador',
            4 => 'valor_antes_iva',
            5 => 'iva',
            6 => 'valor_a_pagar'
        );


        // Se valida que existan registros y se realiza un ordenamiento
        $limit = $request->length??10;
        $offset = $request->start??0;
        $order = $columns[$request->order["0"]["column"]??0] ?? "id";
        $dir   = $request->order["0"]["dir"] ?? "DESC";

        return new FacturaCollection (Factura::getFactura($limit, $offset, $order, $dir));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $factura = new Factura();
        return View::make('factura.save')->with('factura', $factura);
    }

    /**
     * Store a newly created resource in storage.
     * Se realizan validaciones de campos vacios y en los json que tengan sus respectivos campos (emisor, comprador, datos facturado)
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            //Validación de campos vacios
            $validator = Validator::make($request->all(), [
                'emisor' => 'required|string|max:45',
                'comprador' => 'required|string|max:45',
                'valor_antes_iva' => 'required|string|max:15',
                'iva' => 'required|string|max:15',
                'valor_a_pagar' => 'required|string|max:15',
                'items_facturados' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(["status" => "error", "message" => $validator->errors()], 422);
            }
            $emisor = json_decode($request->emisor, true);
            $comprador = json_decode($request->comprador, true);
            $items_facturados = json_decode($request->items_facturados, true);

            // validación de datos completos en el json
            if (empty($emisor['name']) || empty($emisor['nit'])) {
                return response()->json(["status" => "error", "message" => ['emisor' => 'El emisor debe tener nombre y NIT']], 422);
            }

            if (empty($comprador['name']) || empty($comprador['nit'])) {
                return response()->json(["status" => "error", "message" => ['comprador' => 'El comprador debe tener nombre y NIT']], 422);
            }

            //Valida ciclicamente los datos del json correspondientes a los items facturados
            foreach ($items_facturados as $key => $item) {
                if (
                    empty($item['descripcion'])  || empty($item['cantidad'])
                    || empty($item['valor_unitario']) || empty($item['valor_total'])
                ) {
                    return response()->json(["status" => "error", "message" => ['items_facturados' => 'Todos los items facturados deben estar llenos']], 422);
                }
            }

            $data = [
                'emisor' => json_encode($emisor),
                'comprador' => json_encode($comprador),
                'valor_antes_iva' => $request->valor_antes_iva,
                'iva' => $request->iva,
                'valor_a_pagar' => $request->valor_a_pagar,
                'items_facturados' => json_encode($items_facturados)
            ];

            Factura::create($data);
            return response()->json(["status" => "success"]);
        } catch (\Throwable $th) {
            return response()->json(["status" => "error", "message" => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * 
     * Solo se muestra la factura solicitada, su identificador es enviado por parametro, en caso de no existir responde con error
     * 
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function show($id_factura)
    {
        // Valida si la factura existe, en caso contrario responderá con error
        $factura = Factura::find($id_factura);
        if(!empty($factura)) { 
            return new FacturaResource ($this->repository->find($id_factura));
        } 
        else{ 
            return response()->json(["status" => "error", "Factura no encontrada" ]);
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * Actualiza solo la factura solicitada, el identificador es enviado por parametro, en caso de no existir responde con error
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_factura)
    {
        print_r("Actualización de factura: ".$id_factura); die();
        
        try {

            //Validación de campos vacios
            $validator = Validator::make($request->all(), [
                'emisor' => 'required|string',
                'comprador' => 'required|string',
                'valor_antes_iva' => 'required|string|max:15',
                'iva' => 'required|string|max:15',
                'valor_a_pagar' => 'required|string|max:15',
                'items_facturados' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                return response()->json(["status" => "error", "message" => $validator->errors()], 422);
            }

            $emisor = json_decode($request->emisor, true);
            $comprador = json_decode($request->comprador, true);
            $items_facturados = json_decode($request->items_facturados, true);

            // validación de datos completos en el json
            if (empty($emisor['name']) || empty($emisor['nit'])) {
                return response()->json(["status" => "error", "message" => ['emisor' => 'El emisor debe tener nombre y NIT']], 422);
            }

            if (empty($comprador['name']) || empty($comprador['nit'])) {
                return response()->json(["status" => "error", "message" => ['comprador' => 'El comprador debe tener nombre y NIT']], 422);
            }

            // Valida ciclicamente los datos del json correspondientes a los items facturados
            foreach ($items_facturados as $key => $item) {
                if (
                    empty($item['descripcion'])  || empty($item['cantidad'])
                    || empty($item['valor_unitario']) || empty($item['valor_total'])
                ) {
                    return response()->json(["status" => "error", "message" => ['items_facturados' => 'Todos los items facturados deben estar llenos']], 422);
                }
            }


            $factura = Factura::find($id_factura);
            // Getting values from the blade template form
            $factura->emisor =  json_encode($emisor);
            $factura->comprador = json_encode($comprador);
            $factura->valor_antes_iva = $request->valor_antes_iva;
            $factura->iva = $request->iva;
            $factura->valor_a_pagar = $request->valor_a_pagar;
            // $factura->items_facturados = json_encode($items_facturados); //No se permite actualizar los items facturados
            $factura->save();

            return redirect('/factura')->with('success', 'Factura actualizada.');
        } catch (\Throwable $th) {
            return response()->json(["status" => "error", "message" => $th->getMessage()]);
        }
 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Factura  $factura
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_factura)
    {

        // Valida si la factura existe, en caso de no existir responde con error
        $factura = Factura::find($id_factura);
        if(!empty($factura)) { 
            $factura->delete(); 
        } 
        else{ 
            return response()->json(["status" => "error", "Factura no encontrada" ]);
        }

        return response()->json(["status" => "success", "Factura eliminada con éxito" ]);

    }
}
