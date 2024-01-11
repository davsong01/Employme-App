<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{

    public function logWallet($data){
        $wallet = Wallet::create($data);
        return $wallet;
    }

    public function participantWalletIndex($user_id){
        $wallets = $this->getWalletHistory($user_id);
        $balance = $this->getWalletBalance($user_id);
        return view('dashboard.student.payments.wallets', compact('wallets', 'balance'));
    }

    public function getWalletHistory($user_id=null, $status=null){
        $wallets = Wallet::with('user')->orderBy('created_at','DESC');
        
        if(!empty($user_id)){
            $wallets = $wallets->where('user_id', $user_id);
        }
        if(!empty($status)){
            $wallets = $wallets->where('status', $status);
        }

        return $wallets->get();
    }

    public function getWalletBalance($user_id){
        $credits = Wallet::where(['user_id' => auth()->user()->id, 'type' => 'credit', 'status' => 'approved'])->sum('amount');
        $debits = Wallet::where(['user_id' => auth()->user()->id, 'type' => 'debit', 'status' => 'approved'])->sum('amount');
        $balance = $credits - $debits;
        return $balance;
    }

    public function getGlobalWalletBalance(){
        $credits = Wallet::where(['type' => 'credit', 'status' => 'approved'])->sum('amount');
        $debits = Wallet::where(['type' => 'debit', 'status' => 'approved'])->sum('amount');
        $balance = $credits - $debits;
        return $balance;

    }

    public function updateWallet($transaction_id, $data){
        $wallet = Wallet::where('transaction_id', $transaction_id)->update($data);

        if($wallet){
            return 'success';
        }else{
            return 'failed';
        }
    }
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function show(Wallet $wallet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function edit(Wallet $wallet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wallet $wallet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Wallet  $wallet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wallet $wallet)
    {
        //
    }

    public function participantIndex(){
        $wallets = Wallet::where('user_id', auth()->user()->id);

        

    }
}
