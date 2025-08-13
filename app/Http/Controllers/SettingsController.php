<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
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
        $templates = \DB::table('frontend_templates')->get();
        $setting = Settings::first();
       
        return view('dashboard.admin.settings.edit', compact('setting', 'templates'));
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
            'site_access_settings' => 'required'
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

    public function blacklistIndex(Request $request){
        $blacklists = Blacklist::latest()->when($request->filled('value'), function ($query) use ($request) {
            $query->where('value', $request->value);
        })->get();

        return view('dashboard.admin.blacklists.index', compact('blacklists'));

    }

    public function blacklistCreate(){
        return view('dashboard.admin.blacklists.create');
    }

    public function blacklistEdit(Blacklist $blacklist)
    {
        return view('dashboard.admin.blacklists.edit', compact('blacklist'));
    }

    public function blacklistStore(Request $request)
    {
        $validated = $request->validate([
            'type'  => 'required|string|max:50', // e.g., email, phone, ip, etc.
            'value' => 'required|string|max:255|unique:blacklists,value',
            'reason' => 'nullable|string|max:255',
        ]);

        $validated['added_by'] = resolveAuthUser()->id;
        // Create blacklist entry
        Blacklist::create($validated);

        return redirect()
            ->route('blacklist.index')
            ->with('message', 'Created successfully');
    }

    public function blacklistUpdate(Request $request, Blacklist $blacklist)
    {
        $validated = $request->validate([
            'type'   => 'required|string|max:50',
            'value'  => 'required|string|max:255|unique:blacklists,value,' . $blacklist->id,
            'reason' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $blacklist->update($validated);

        return redirect()
            ->route('blacklist.index')
            ->with('message', 'Updated successfully');
    }

    public function blacklistDestroy(Blacklist $blacklist)
    {
        $blacklist->delete();

        return back()->with('message', 'Delete successful');
    }
}
