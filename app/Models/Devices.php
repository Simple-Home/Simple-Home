<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\Properties;
use App\Helpers\SettingManager;
use DateTime;

class Devices extends Model
{
    // protected $approved = [
    //     'Unapproved',
    //     'Approved',
    //     'Blocked',
    // ];

    protected $table = 'sh_devices';

    public function getProperties()
    {
        return $this->hasMany('App\Models\Properties', 'device_id');
    }

    public function setHeartbeat()
    {
        $this->heartbeat = new DateTime();
        $this->save();
    }

    /**
     * check if device if offline
     *
     * @return bool
     */

    public function getOfflineAttribute()
    {
        $offline = false;

        $heartbeat = new DateTime($this->heartbeat);
        $sleep = empty($this->sleep) ? 1 : $this->sleep;
        $heartbeat->modify('+' . $sleep . ' ms');
        $heartbeat->modify('+5 s');
        $now = new DateTime();

        if ($heartbeat->getTimestamp() < $now->getTimestamp()) {
            $offline = true;
        }
        return $offline;
    }

    public function getPropertiesExistence($type)
    {
        $property = $this->getProperties()->where('type', $type)->first();
        if (isset($property->type) && $property->type == $type) {
            return true;
        }
        return false;
    }

    public function getHostname()
    {
        return str_replace(" ", "_", strtolower($this->hostname));
    }

    public function getIntegrationAttribute($value)
    {
        return strtolower($value);
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getRateLimitAttribute()
    {
        $rate = 1000;
        if (!empty($this->sleep) && $this->sleep > 0) {
            $rate = (60 / ($this->sleep / 1000));
        }
        return $rate;
    }

    public function getSignalStrengthAttribute()
    {
        $RSSI = ($this->getProperties->where('type', 'wifi')->first());
        if ($RSSI && !empty($RSSI->last_value)) {
            return $RSSI->last_value->value;
        }

        return false;
    }

    public function getSignalStrengthPercentAttribute()
    {
        $RSSI = ($this->getProperties->where('type', 'wifi')->first());
        if ($RSSI && !empty($RSSI->last_value)) {
            // dBm to Quality:
            if ($RSSI->last_value->value <= -100) {
                return 0;
            } else if ($RSSI->last_value->value >= -50) {
                return ($RSSI->last_value->value = 100);
            } else {
                return (2 * ($RSSI->last_value->value + 100));
            }
        }

        return false;
    }

    public function getBatteryLevelAttribute()
    {
        $BatteryValue = $this->getProperties->where('type', 'battery')->first();

        if ($BatteryValue) {
            return $BatteryValue->last_value->value;
        }
        return false;
    }

    public function getBatteryLevelPercentAttribute()
    {
        $batteryVoltage = ($this->getProperties->where('type', 'battery')->first());

        if ($batteryVoltage && $batteryVoltage->last_value) {
            $max = $batteryVoltage->max_value;
            $min = $batteryVoltage->min_value;
            $max = ($max - $min);
            $onePercent = $max / 100;
    
            return ($batteryVoltage->last_value->value - $min) / $onePercent;
        }
        return false;
    }

    public function getSettingsCountAttribute()
    {
        $settings = Settings::where('group', '=', 'device-' . $this->id)->get();
        if ($settings) {
            return $settings->count();
        }
        return false;
    }

    public function reboot()
    {
        $this->command = "reset";
        $this->save();
    }

    public function approve()
    {
        $this->approved = "1";
        $this->save();
    }

    public function disapprove()
    {
        $this->approved = "0";
        $this->save();
    }

    use HasFactory;
}
