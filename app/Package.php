<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'user_id',
        'nights',
        'start_date',
        'start_place',
        'adults',
        'children_count',
        'number_of_destinations',
        'countries',
        'children',
        'transfer',
        'status',
        'note'
    ];

    protected $casts = [
        'countries' => 'collection',
        'children' => 'collection',
        'transfer' => 'boolean'
    ];

    public function accommodationRequests(){
        return $this->hasMany(PackageAccommodation::class);
    }

    public function transferRequests(){
        return $this->hasMany('App/TransferRequest');
    }

    public function client(){
        return $this->belongsTo('App/Client');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country(){
        return $this->belongsToMany(Country::class,'country_package','country_id','package_id');
    }

    public function savePackageDetails($data){

        $packageCountries=[];
        $this->start_date=Carbon::parse($data['package_details']['tripStartAt']);
        $this->start_place=$data['package_details']['startPlace'];
        $this->adults=$data['package_details']['adultsNum'];
        $this->children_count=$data['package_details']['childrenNumber'];
        $this->number_of_destinations=$data['package_details']['placesNum'];
        $this->transfer=$data['package_details']['transfer'];
        $this->note=$data['note'];
        $this->children=json_encode($data['package_details']['childrenAges']);
        $this->client_id=$data['client_details']['id'];
        $this->user_id=$data['user_id'];
        $this->save();
        foreach($data['package_details']['selectedCountries'] as $selectedCountry){
            array_push($packageCountries,$selectedCountry['id']);
        }

        $this->country()->attach($packageCountries);
        foreach ($data['destination_details'] as $destination_detail){
            $other_services=[];
            $rooms_data=[];

            $services= new \stdClass();
            $services->rent_car=$destination_detail['rentCar'];
            $services->car_with_driver=$destination_detail['rentCarWithDriver'];
            $services->car_level=$destination_detail['selectedCarLevel'];
            $services->need_tours=$destination_detail['needTours'];
            array_push($other_services,$services);
            if(array_key_exists('roomsNum',$destination_detail['hotelDetails'])){
                for($i=0;$i<count($destination_detail['hotelDetails']['roomsNum']);$i++){
                    $rooms=new \stdClass();
                    $rooms->room_number=$i+1;
                    $rooms->adults=$destination_detail['hotelDetails']['selectedAdultsNum'][$i];
                    $rooms->children=$destination_detail['hotelDetails']['selectedChildrenNum'][$i];
                    array_push($rooms_data,$rooms);

                }

            }

            $this->accommodationRequests()->create([
                'accommodation_type'=>$destination_detail['selectedAccomodationType'],
                'checkin'=>Carbon::parse($destination_detail['checkInDate']),
                'checkout'=>Carbon::parse($destination_detail['checkOutDate']),
                'nights'=>$destination_detail['nightsNum'],
                'city_id'=>$destination_detail['selectedCity']['id'],
                'country_id'=>$destination_detail['selectedCity']['country_id'],
                'rooms'=>json_encode($rooms_data),
                'hotel_name'=>array_key_exists('hotelName',$destination_detail['hotelDetails'])?$destination_detail['hotelDetails']['hotelName']:null,
                'area_name'=>array_key_exists('area',$destination_detail['hotelDetails'])?$destination_detail['hotelDetails']['area']:null,
                'room_type'=>array_key_exists('selectedRoomType',$destination_detail['hotelDetails'])?$destination_detail['hotelDetails']['selectedRoomType']:null,
                'room_view'=>array_key_exists('selectedRoomView',$destination_detail['hotelDetails'])?$destination_detail['hotelDetails']['selectedRoomView']:null,
                'stars'=> array_key_exists('selectedStars',$destination_detail['hotelDetails'])?$destination_detail['hotelDetails']['selectedStars']:null,
                'other_services'=>json_encode($other_services)
            ]);
        }




    }
}

