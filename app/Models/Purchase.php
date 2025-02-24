<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';
    protected $guarded = ['id'];



    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id', 'id');
    }
}
