<?php

namespace App\Models;

use App\Models\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Helpers\SettingManager;

class Location extends Properties
{
    protected $historyDefault = 10;
    protected $unitsDefault = "";
    protected $iconDefault = "fas fa-globe-europe";

    public function save(array $options = [])
    {
        $this->setDefaultValues();
        // before save code 
        $result = parent::save($options); // returns boolean
        // after save code
        return $result; // do not ignore it eloquent calculates this value and returns this, not just to ignore
    }

    public function update(array $attributes = [], array $options = [])
    {
        $this->setDefaultValues();
        // before save code 
        $result = parent::update($attributes, $options); // returns boolean
        // after save code
        return $result; // do not ignore it eloquent calculates this value and returns this, not just to ignore

    }

    private function setDefaultValues()
    {
        if ($this->icon == "" || $this->icon == "empty")
            $this->icon = $this->iconDefault;

        if ($this->history == 0)
            $this->history = $this->historyDefault;

        if ($this->units == "")
            $this->units = $this->unitsDefault;
    }

    public function getLocation(){
        //TODO: DINAMICALY LOAD FOR EACH USER
        $places = $this->getPlaces();
        $lat = explode(",", $this->latestRecord->value)[0];
        $long = explode(",", $this->latestRecord->value)[1];

        foreach ($places as $place){
            $latDestination = explode(",", $place->value)[0];
            $longDestination = explode(",", $place->value)[1];
            if ($this->getDistance($lat, $long, $latDestination, $longDestination) < 70) {
                return $place->name;
            }
        }
    }

    private function getPlaces(){
        return Cache::remember('location.places', 120, function (){
            return SettingManager::getGroup('locations');
        });
    }

    private function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo){ 
        $pi = pi(); 
        $x = sin($latitudeFrom * $pi/180) * 
        sin($latitudeTo * $pi/180) + 
        cos($latitudeFrom * $pi/180) * 
        cos($latitudeTo * $pi/180) * 
        cos(($longitudeTo * $pi/180) - ($longitudeFrom * $pi/180)); 
        $x = atan((sqrt(1 - pow($x, 2))) / $x); 
        $mi = abs((1.852 * 60.0 * (($x/$pi) * 180)) / 1.609344); 
        return ($mi / 0.0006214);
    } 
}
