<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Complain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\facades\DB;

class ComplainController extends Controller
{
    public function index()
    {
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
            $i = 1;  
            $complains = Complain::all();
            $resolvedComplains =  Complain::where('status', '=', 'Resolved' )->count();
            $pendingComplains =  Complain::where('status', '=', 'Pending' )->count(); 
            $InProgressComplains =  Complain::where('status', '=', 'In Progress' )->count();  
            
            
            return view('dashboard.admin.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains'));
        }elseif(Auth::user()->role_id == "Student"){    
                $resolvedComplains =  Complain::where('user_id', '=', Auth::user()->id )->where('status', '=', 'Resolved' )->count();
                $pendingComplains =  Complain::where('user_id', '=', Auth::user()->id )->where('status', '=', 'Pending' )->count(); 
                $InProgressComplains =  Complain::where('user_id', '=', Auth::user()->id )->where('status', '=', 'In Progress' )->count();          
                $complains = Complain::where('user_id', '=', Auth::user()->id)->orderBy('created_at', 'DESC')->get();

                return view('dashboard.student.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains'));
    } else return back();
}

    public function create()
    {
          if(Auth::user()->role_id == "Admin"){
            return view('dashboard.admin.complains.create')->with('extend', 'dashboard.admin.index');

    }elseif(Auth::user()->role_id == "Student"){
        return view('dashboard.admin.complains.create')->with('extend', 'dashboard.student.index');
} return back();
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required|min:5|max:50',
            'email' => 'required',
            'phone' => 'required',
            'state' => 'required',
            'lga' => 'required',
            'address' => 'required|min:5|max:60',
            'mode' => 'required',
            'type' => 'required',
            'priority' => 'required',
            'status' => 'required',
            'gender' => 'required',
            'teamlead' => 'required',
            'complain' => 'required',
            'other' => 'sometimes',
        ]);

        Complain::create([
            'user_id' => Auth::user()->id,
            'name' => $data['name'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'state' => $data['state'],
            'lga' => $data['lga'],
            'other' => $data['other'],
            'mode' => $data['mode'],
            'type' => $data['type'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'gender' => $data['gender'],
            'teamlead' => $data['teamlead'],
            'status' => 'Pending',
            'content' => $data['complain']
        ]);

        return back()->with('message', 'CRM has been created succesfully');
    }

    public function show(Complain $complain)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Complain $complain)
    {
        
        if(Auth::user()->role_id == "Admin"){
            return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.admin.index');
    }elseif (Auth::user()->role_id == "Student"){
        return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.student.index');
}return back();
    }

    public function update(Complain $complain, Request $request)
    {

        $complain->update($request->all());

        //Update User Percentage Response
        $this->percentage($complain->user_id);

        return back()->with('message', 'Complain has been updated');
    }

    public function destroy(Complain $complain)
    {
        $complain->delete();

        return back()->with('message', 'Complain has been deleted');
    }
    
    public function resolve(Complain $complain)
    {

        $complain->status = 'Resolved';
    
        $complain->save();
        
        //Update User Percentage Response
        $this->percentage($complain->user_id);

        return back()->with('message', 'CRM has been marked as Resolved');
    }
     
    private function percentage($id){       
        //count number of complains for assignee
        $totalComplains = Complain::where('user_id', $id)->count();
       
        //count number of resolved cases for assignee
        $resolvedComplains = Complain::where('user_id', $id)->where('status', 'Resolved')->count();

        //find the percentage resolved
        $responsePercentage = ($resolvedComplains / $totalComplains) * 100;

        //update user percentage
        $user = User::findorfail($id);
        $user->responseStatus = $responsePercentage;
        $user->save();

    }
}
