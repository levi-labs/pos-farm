<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierService
{
    public function getAll()
    {
        return Supplier::all();
    }

    public function getById(Supplier $supplier)
    {
        return Supplier::find($supplier->id);
    }

    public function create(Request $request)
    {
        return Supplier::create($request->all());
    }

    public function update(Request $request, Supplier $supplier)
    {
        return $supplier->update($request->all());
    }

    public function delete(Supplier $supplier)
    {
        return $supplier->delete();
    }
}
