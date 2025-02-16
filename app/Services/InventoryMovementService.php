<?php

namespace App\Services;

use App\Models\InventoryMovement;

class InventoryMovementService
{
    public function outStock(array $data)
    {
        return InventoryMovement::create($data);
    }

    public function inStock(array $data)
    {
        return InventoryMovement::create($data);
    }

    public function getAllOutStock()
    {
        return InventoryMovement::where('movement_type', 'out')->get();
    }

    public function getAllInStock()
    {
        return InventoryMovement::where('movement_type', 'in')->get();
    }
}
