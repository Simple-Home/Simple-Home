<?php
namespace App\PropertyTypes\speaker;

use App\PropertyTypes\PropertyTypes;

/**
 * Class speaker
 * @package App\PropertyTypes\speaker
 */
abstract class speaker extends PropertyTypes
{
    private $speaker;
    abstract public function state($value, $args);
    abstract public function play();
    abstract public function pause();
    abstract public function forward();
    abstract public function reverse();
    abstract public function next();
    abstract public function back();
    abstract public function mute($value);
    abstract public function volume($value);
    public function allowedValues(){ 
        return [
                "state"=>["on","off"], 
                "play"=>[],
                "pause"=>[],
                "forward"=>[],
                "reverse"=>[],
                "next"=>[],
                "back"=>[],
                "mute"=>["on", "off"],
                "volume"=>["1","2","3","4","5","6","7","8","9","10"]
                ]; 
    }
}