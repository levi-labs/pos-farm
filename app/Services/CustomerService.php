<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{

    public function getAll()
    {
        return Customer::all();
    }

    public function getById($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            return $customer;
        }
        return false;
    }

    public function create($data)
    {
        return Customer::create($data);
    }

    public function update($id, $data)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->update($data);
            return $customer;
        }
        return false;
    }

    public function delete($id)
    {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            return true;
        }
        return false;
    }
}
