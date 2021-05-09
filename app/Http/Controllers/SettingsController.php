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
            'OFFICIAL_EMAIL' => 'required|email',
            'ADDRESS_ON_RECEIPT' => 'required',
            'CURR_ABBREVIATION' => 'required',
            'DEFAULT_CURRENCY' => 'required',
            'primary_color' => 'required|regex:/^#[\daA-fF]{6}/i',
            'secondary_color' => 'required|regex:/^#[\daA-fF]{6}/i',
            'logo' => 'nullable|image|mimes:png',
            'favicon' => 'nullable|image|mimes:png',
            'banner' => 'nullable|image'
        ]);

        if($request->has('logo') && $request->file('logo')){
            Image::make($request->logo)->resize(152, 60)->save('assets/images/logo-text.png', 80, 'png');
            Image::make($request->logo)->resize(270, 92)->save('login_files/assets/images/logo.png', 80, 'png');
        }

        if($request->has('banner') && $request->file('banner')){
            Image::make($request->banner)->resize(270, 92)->save('login_files/assets/images/picture.jpg', 80, 'png');
        }


        if($request->has('favicon') && $request->file('favicon')){
            Image::make($request->favicon)->resize(16, 16)->save('assets/images/favicon.png', 80, 'png');
        }

        $setting->update([
            'OFFICIAL_EMAIL' => $request->OFFICIAL_EMAIL,
            'ADDRESS_ON_RECEIPT' => $request->ADDRESS_ON_RECEIPT,
            'CURR_ABBREVIATION' => $request->CURR_ABBREVIATION,
            'DEFAULT_CURRENCY' => $request->DEFAULT_CURRENCY,
            'primary_color' => $request->primary_color,
            'secondary_color' => $request->secondary_color
        ]);

        return back()->with('message', 'Update successful');
    }


    public function destroy(Settings $settings)
    {
        //
    }
}
