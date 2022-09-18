<?php

namespace App\Http\Controllers\Admin;

use App\Settings;
use App\PaymentMode;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class PaymentModeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $i = 1;
        $modes = PaymentMode::orderBy('created_at', 'DESC')->get();

        return view('dashboard.admin.payment_modes.index', compact('modes', 'i'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currency = Settings::value('CURR_ABBREVIATION');
        $currency_symbol = Settings::value('DEFAULT_CURRENCY');

        return view('dashboard.admin.payment_modes.create', compact('currency', 'currency_symbol'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'type' => 'required',
            'public_key' => 'required',
            'secret_key' => 'required',
            'merchant_email' => 'required|email',
            'currency' => 'required',
            'currency_symbol' => 'required',
            'exchange_rate' => 'required',
            'processor' => 'required',
            'name' => 'required|unique:payment_modes|string|max:30',
            'image' => 'sometimes'
        ]);
        
        $data['slug'] = Str::slug($data['name']);
        if (request()->has('image')) {
            $imageFile = Image::make($request->image)->resize(200, 150);
            $imageName = rand(111111111, 999999999);
            $imageFile->save('paymentmodes/' . $imageName . '.jpg');
            $data['image'] = $imageName . '.jpg';
        }

        // dd($data);
        PaymentMode::create($data);

        return redirect(route('payment-modes.index'))->with('message', 'Operation successful');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentMode $paymentMode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentMode $paymentMode)
    {
        $currency = Settings::value('CURR_ABBREVIATION');
        // $currency_symbol = Settings::value('DEFAULT_CURRENCY');
       
        return view('dashboard.admin.payment_modes.edit', compact('currency', 'paymentMode'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentMode $paymentMode)
    {
       
        $data =$this->validate($request, [
            'name' => 'required|string|max:30|unique:payment_modes,name,' . $paymentMode->id,
            'merchant_email' => 'required|email',
            'currency' => 'required',
            'currency_symbol' => 'required',
            'exchange_rate' => 'required',
            'processor' => 'required',
            'type' => 'required',
            'image' => 'sometimes',
            'status' => 'required',
            'secret_key' => 'required',
        ]);
       
        $request['slug'] = Str::slug($data['name']);
        if (request()->has('image')) {
            $imageFile = Image::make($request->image)->resize(200, 150);
            $imageName = rand(111111111, 999999999);
            $imageFile->save('paymentmodes/' . $imageName . '.jpg');
            $data = $request->except(['image', '_method', '_token']);
            $data['image'] = $imageName . '.jpg';
        }
        
        $paymentMode->update($data);
        // dd($data,$paymentMode);
        return redirect(route('payment-modes.index'))->with('message', 'Operation successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\PaymentMode  $paymentMode
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentMode $paymentMode)
    {
        $paymentMode->delete();

        return back()->with('message', 'Delete operation successful');
    }
}
