<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $i = 1;
        $currencies = Currency::orderBy('created_at', 'DESC')->get();

        return view('dashboard.admin.currency.index', compact('currencies', 'i'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.admin.currency.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'country_name' => 'required',
            'conversion_rate' => 'required',
            'symbol' => 'required',
            'symbol_native' => 'required',
            'status' => 'required',
        ]);

        // dd($data);
        Currency::create($data);

        return redirect(route('currency.index'))->with('message', 'Operation successful');
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('dashboard.admin.currency.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'country_name' => 'required',
            'conversion_rate' => 'required',
            'symbol' => 'required',
            'symbol_native' => 'required',
            'status' => 'required',
        ]);

        $currency->update($data);
        // dd($data,$paymentMode);
        return redirect(route('currency.index'))->with('message', 'Operation successful');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        //
    }
}
