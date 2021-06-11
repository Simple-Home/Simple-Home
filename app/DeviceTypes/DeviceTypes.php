<?php

namespace App\DeviceTypes;

/**
 * Class DeviceTypes
 * @package App\DeviceTypes
 */
abstract class DeviceTypes
{
    protected $deviceTypes;
    protected $meta;
    public $features;
    private $state = array();
    private $properties = array();

    /**
     * getFeatures
     * Get all supported features and return as array
     * 
     * @param mixed $classObject
     * @return void
     */
    public function getFeatures($classObject)
    {
        $class = new \ReflectionClass(get_class($classObject)); // Create Reflection Object
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC); // Retrieve all public methods
        $features = array();

        if(sizeof($methods) > 0){ // If > 0 features have been found
            for($i = 0; $i < sizeof($methods); ++$i){
                if ($methods[$i]->name != 'getFeatures' && $methods[$i]->name != '__construct') {
                    $features[$i]['name'] = $methods[$i]->name;
                }
            }
        }
        return $features;
    }


    /**
     * hasFeature
     * Check if an device has the requested feature
     * 
     * @param mixed $classObject
     * @param mixed $feature
     * @return void
     */
    public function hasFeature($classObject, $feature)
    {
        $features = $this->getFeatures($classObject);

        foreach($features as $c){
            if (strtolower($c['name']) == strtolower($feature)) {
                return true;
            }
        }
        return false;
    }

    /**
     * allowedValue
     * Check if an device feature supports the requested value
     * 
     * @param $classObject
     * @param $feature
     * @param $value
     * @return void
     */
    public function allowedValue($classObject, $feature, $value = null)
    {
        $allowedValues = $classObject->allowedValues()[$feature];
        return ($value==null) ? implode(", ", $allowedValues) : (in_array($value, $allowedValues) ? "allowed" : "");
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest(){
        return $this->device->hasFeature($this->device, $feature);
    }
    
    /**
     * getState
     * 
     * @param mixed feature
     * @return void
     */
    public function getState($feature = null){  
        if(isset($feature)){
            if(array_key_exists($feature, $this->state)) {
                return $this->state[$feature];
            }else{
                return null;
            }
        }else{
            return $this->state;
        }
    }

    /**
     * setStatus
     * 
     * @param mixed feature
     * @param mixed state
     * @return void
     */
    public function setState($feature, $state)
    {   
        
        if(empty($state)) return "nul";

        if ($feature == "All"){
            $this->state = $state;
        }else{
            $this->state[$feature] = $state;
        } 
    }

    /**
     * setProperties
     * 
     * @param mixed name
     * @param mixed value
     * @return void
     */
    public function setProperties($name, $value){
        if (in_array($name, $this->supportedProperties)){
            $this->properties[$name] = $value;
            $this->state["properties"] = $this->properties;   
        }else{
            return "Property Not Supported";
        }
    }

}