<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    //
    public $incrementing = false;
    protected $table = 'advertisement';
    protected $fillable = ['advertisement_id','advertisement_name','advertisement_img','staff_id'];
    
    protected $primaryKey = 'advertisement_id';
    
    public $timestamps = false;

    public function Staff(){
    	return $this->belongTo('App\Staff','staff_id');
    }
}
