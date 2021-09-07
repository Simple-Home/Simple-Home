<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Rooms;
use App\Models\Properties;
use Kris\LaravelFormBuilder\FormBuilder;
use App\Helpers\SettingManager;
use Spatie\Backup\Tasks\Cleanup\Period;
use App\Types\GraphPeriod;

class ControlsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function list($room_id = 0, FormBuilder $formBuilder)
    {
        $rooms = Rooms::all();
        $roomsForm = [];
        $roomForm = $formBuilder->create(\App\Forms\RoomForm::class, [
            'method' => 'POST',
            'url' => route('rooms.store'),
        ], ['edit' => false]);

        $rooms = Rooms::all()->filter(function ($item) {
            //if ($item->PropertiesCount > 0) {
            return $item;
            //}
        });

        if ($room_id == 0)
            $room_id =  Rooms::min('id');

        $propertyes =  Properties::where("room_id", $room_id)->get()->filter(function ($item) {
            if ($item->device->approved == 1) {
                return $item;
            }
        });

        return view('controls.list', compact('rooms', 'propertyes', 'roomForm'));
    }

    public function detail($property_id, $period = GraphPeriod::DAY)
    {
        $property = Properties::find($property_id);

        $dataset["data"] = [];
        $labels = [];

        $property->period = $period;
        foreach ($property->agregated_values  as $key => $item) {
            $dataset["data"][] += $item->value;
            $labels[] = $item->created_at->diffForHumans();
        }
        $dataset["fill"] = True;
        $dataset["backgroundColor"] = "rgba(220,220,220,0.5)";
        $dataset["borderColor"] = "rgba(220,220,220,1)";
        $dataset["tension"] = 0.4;
        $dataset["pointRadius"] = 0;


        $propertyDetailChart = app()->chartjs
            ->name('propertyDetailChart')
            ->type('line')
            ->labels($labels)
            ->datasets([$dataset])
            ->optionsRaw("{
                plugins:{
                    legend:{
                        display: false
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            min: Math.min.apply(this, " . json_encode($dataset["data"]) . ") - 5,
                            max: Math.max.apply(this, " . json_encode($dataset["data"]) . ") + 5
                        }
                    }]
                }
            }");

        return view('controls.detail', ["table" => $property->agregated_values, "property" => $property, "propertyDetailChart" => $propertyDetailChart]);
    }

    public function edit($property_id, FormBuilder $formBuilder)
    {
        $rooms = Rooms::all();
        $sortRooms = array();
        foreach ($rooms as $room) {
            $sortRooms[$room->id] = $room->name;
        }
        $property = Properties::find($property_id);

        $propertyForm = $formBuilder->create(\App\Forms\PropertyForm::class, [
            'model' => $property,
            'method' => 'POST',
            'url' => route('controls.update', ['property_id' => $property_id]),
        ], ['icon' => $property->icon, 'rooms' => $sortRooms]);


        $settings = SettingManager::getGroup('property-' . $property_id);
        $systemSettingsForm = $formBuilder->create(\App\Forms\SettingDatabaseFieldsForm::class, [
            'method' => 'POST',
            'url' => route('controls.settings.update', $property_id),
            'variables' => $settings
        ]);

        return view('controls.edit', compact('property', 'propertyForm', 'systemSettingsForm'));
    }

    public function settingsUpdate(Request $request, FormBuilder $formBuilder)
    {
        foreach ($request->input() as $key => $value) {
            if ($key == '_token') {
                continue;
            }
            SettingManager::set($key, $value);
        }

        return redirect()->back()->with('success', 'Property settings sucessfully removed.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $property_id, FormBuilder $formBuilder)
    {
        $form = $formBuilder->create(\App\Forms\PropertyForm::class);

        if (!$form->isValid()) {
            return redirect()->back()->withErrors($form->getErrors())->withInput();
        }

        $property = Properties::find($property_id);
        $property->nick_name = $request->input('nick_name');
        $property->icon = $request->input('icon');
        $property->history = $request->input('history');
        $property->units = $request->input('units');
        $property->room_id = $request->input('room_id');
        $property->save();

        return redirect()->route('controls.edit', ['property_id' => $property_id])->with('success', 'Property settings sucessfully removed.');;
    }

    public function remove($property_id)
    {
        $property = Properties::find($property_id);
        $property->delete();

        return redirect()->route('controls.room')->with('danger', 'Property Sucessfully removed.');
    }
}
