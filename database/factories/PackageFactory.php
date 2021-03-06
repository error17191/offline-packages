<?php

use Faker\Generator as Faker;

$factory->define(App\Package::class, function (Faker $faker) {
    return [
    'client_id'=>rand(1,100),
    'user_id'=>rand(1,100),
    'title'=>$faker->title,
    'start_date'=>$faker->date(),
    'end_date'=>$faker->date(),
    'nights'=>rand(3,20),
    'adults'=>rand(1,20),
    'children_count'=>rand(0,3),
    'number_of_destinations'=>rand(1,10),
    'countries'=>json_encode(['code'=>$faker->countryCode,'name'=>$faker->country]),
    'children'=>null,
    'transfer'=>false,
    'status'=>"s1",
    'start_place'=>null,
    'note'=>null

    ];
});
