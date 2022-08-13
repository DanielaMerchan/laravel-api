<?php

namespace App\Repositories;

use App\Models\Factura;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FacturaRepository implements FacturaRepositoryInterface
{
    protected $model;

    /**
     * FacturaRepository constructor.
     *
     * @param Factura $factura
     */
    public function __construct(Factura $factura)
    {
        $this->model = $factura;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)
            ->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        if (null == $factura = $this->model->find($id)) {
            throw new ModelNotFoundException("Post not found");
        }

        return $factura;
    }
}