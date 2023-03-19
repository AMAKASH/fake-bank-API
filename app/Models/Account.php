<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;
    protected $hidden = ['pivot', 'acc_balance', 'min_balance'];

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function transfer_history()
    {
        return Transaction::where('sender_id', $this->attributes['id'])
            ->orWhere('recipient_id', $this->attributes['id'])->get();
    }

    public function details()
    {
        $tobeRetured = $this->getAttributes();
        $customers = [];
        foreach ($this->customers as $customer) {
            $temp = [];
            $temp['id'] = $customer->id;
            $temp['name'] = $customer->name;
            array_push($customers, $temp);
        }
        $tobeRetured['customers'] = $customers;
        return $tobeRetured;
    }
}
