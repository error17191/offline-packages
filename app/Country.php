<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function clients(){
        return $this->hasMany('App/Client');
    }
    public function cities(){
        return $this->hasMany("App/City");
    }

    public static  function getCities($country_id){
        $cities=City::whereIn('country_id',$country_id)->orderBy('top_destination','desc')->get();
        return $cities;
    }
}
