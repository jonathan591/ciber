<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class Transaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded=[];

    public function propiedad(){
      return $this->belongsTo(IntellectualProperty::class,'intellectual_property_id');
  
    }
  
    public function user(){
      return $this->belongsTo(User::class);
    }
}
