<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class IntellectualProperty extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'owner_id');
    }

    public function propiedad (){
        return $this->hasMany(Transaction::class);
    }

    public function category (){
        return $this->belongsTo(Category::class);
    }
}
