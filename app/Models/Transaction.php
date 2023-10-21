<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory ,SoftDeletes;



   protected $fillable = [
       'user_id',
       'transaction_type',
       'amount',
       'fee',
       'date',
   ];


   public function currentBalanceInfo()
   {
       return $this->belongsTo('App\Models\User','user_id','id');
   }


}
