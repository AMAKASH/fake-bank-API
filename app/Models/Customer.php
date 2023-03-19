<?php

namespace App\Models;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    protected $hidden = ['pivot'];
    protected $guarded = [];

    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }
}
