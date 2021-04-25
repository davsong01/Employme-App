<?php

namespace App\Http\Controllers;

use App\Settings;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

   
    public function store(Request $request)
    {
        //
    }

  
    public function show(Settings $setting)
    {
        
    }


    public function edit(Settings $setting)
    {

        return view('dashboard.admin.settings.edit', compact('setting'));
    }

  
    public function update(Request $request, Settings $setting)
    {

        $file = 'logo';
        $extension = $request->file('logo')->getClientOriginalExtension();
        $filePath = $request->file('logo')->storeAs('public/assets/images', $file.'.'.$extension  ,'public');

       
        $setting->update([
            'OFFICIAL_EMAIL' => $request->OFFICIAL_EMAIL,
            'ADDRESS_ON_RECEIPT' => $request->ADDRESS_ON_RECEIPT,
            'CURR_ABBREVIATION' => $request->CURR_ABBREVIATION,
            'DEFAULT_CURRENCY' => $request->DEFAULT_CURRENCY,
            'program_coordinator' => $request->program_coordinator
        ]);
        
        return back()->with('message', 'Update successful');
    }

 
    public function destroy(Settings $settings)
    {
        //
    }
}
