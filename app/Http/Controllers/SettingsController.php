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
        $this->validate($request, [
            'OFFICIAL_EMAIL' => 'required|email|unique:users,email,' . $setting->id,
            'ADDRESS_ON_RECEIPT' => 'required',
            'CURR_ABBREVIATION' => 'required',
            'DEFAULT_CURRENCY' => 'required',
            'primary_color' => 'required|regex:/^#[\da-f]{6}/i',
            'secondary_color' => 'required|regex:/^#[\da-f]{6}/i',
        ]);


            $imgName = $request->logo->getClientOriginalName();
            //
            $logo = (string) Image::make($request->logo)->resize(152, 60)->save('assets/images'.'/'.$imgName, 80, 'png');


// $jpg = (string) Image::make('public/'.$file)->encode('png', 90);
        // $image = Image::make($request->logo)->resize(152, 60);


        $setting->update([
            'OFFICIAL_EMAIL' => $request->OFFICIAL_EMAIL,
            'ADDRESS_ON_RECEIPT' => $request->ADDRESS_ON_RECEIPT,
            'CURR_ABBREVIATION' => $request->CURR_ABBREVIATION,
            'DEFAULT_CURRENCY' => $request->DEFAULT_CURRENCY,
        ]);

        return back()->with('message', 'Update successful');
    }


    public function destroy(Settings $settings)
    {
        //
    }
}
