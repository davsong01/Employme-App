<?php

namespace App\Http\Controllers;

use App\Models\Settings;
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
        $templates = \DB::table('frontend_templates')->get();
        
        return view('dashboard.admin.settings.edit', compact('setting', 'templates'));
    }


    public function update(Request $request, Settings $setting)
    {
        $data = $this->validate($request, [
            'OFFICIAL_EMAIL' => 'required|email',
            'ADDRESS_ON_RECEIPT' => 'required',
            'CURR_ABBREVIATION' => 'required',
            'DEFAULT_CURRENCY' => 'required',
            'primary_color' => 'required|regex:/^#[\daA-fF]{6}/i',
            'secondary_color' => 'required|regex:/^#[\daA-fF]{6}/i',
            'logo' => 'nullable|image',
            'favicon' => 'nullable|image',
            'banner' => 'nullable|image',
            'program_coordinator' => 'nullable',
            'token' => 'nullable',
            'frontend_template' => 'sometimes',
            'tac_link' => 'sometimes',
            'contact_link' => 'sometimes',
            'about_link' => 'sometimes',
            'privacy_link' => 'sometimes',
            'facebook_link' => 'sometimes',
            'twitter_link' => 'sometimes',
            'instagram_link' => 'sometimes',
            'phone' => 'sometimes',
            'allow_whatsapp_chat' => 'required',
            'allow_transfer_button' => 'required',
            'email_provider' => 'required',
        ]);

    
        if($request->has('logo') && $request->file('logo')){
            Image::make($request->logo)->resize(152, 60)->save('assets/images/logo.png', 80, 'png');
            $data['logo'] = 'assets/images/logo.png';
        }
       
        if($request->has('banner') && $request->file('banner')){
            Image::make($request->banner)->resize(1280, 853)->save('login_files/assets/images/picture.jpg', 80, 'png');
            $data['banner'] = 'login_files/assets/images/picture.jpg';
        }


        if($request->has('favicon') && $request->file('favicon')){
            Image::make($request->favicon)->resize(16, 16)->save('assets/images/favicon.png', 80, 'png');
            $data['favicon'] = 'assets/images/favicon.png';
        }
       
        $setting->update($data);

        return back()->with('message', 'Update successful');
    }


    public function destroy(Settings $settings)
    {
        //
    }
}
