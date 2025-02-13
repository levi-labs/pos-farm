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

    public function getById($id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return false;
        }
        return $supplier;
    }

    public function create(Request $request)
    {
        return Supplier::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);

        if ($supplier) {
            $supplier->update($request->all());
            return $supplier;
        }
        return false;
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);
        if ($supplier) {
            return $supplier->delete();
        }
        return false;
    }
}
