<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Program;
use App\Models\Complain;
use App\Models\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ComplainController extends Controller
{
    public function index(Request $request)
    {
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $i = 1;
            if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
                $trainings = auth()->user()->trainings;

                if (isset($trainings) && !empty($trainings)) {
                    $trainings = array_column($trainings->toArray(), 'program_id');
                } else {
                    $trainings = [];
                }
                
                $complains = Complain::with('user')->whereIn('program_id', $trainings)->orderBy('user_id', 'DESC')->get();
                $resolvedComplains =  Complain::where('status', '=', 'Resolved')->whereIn('program_id', $trainings)->count();
                $pendingComplains =  Complain::where('status', '=', 'Pending')->whereIn('program_id', $trainings)->count();
                $InProgressComplains =  Complain::where('status', '=', 'In Progress')->whereIn('program_id', $trainings)->count();
            } else {
                $complains = Complain::with('user')->orderBy('user_id', 'DESC')->get();

                // foreach($complains as $c){
                //     $check = \DB::table('program_user')->where('user_id', $c->user_id)->first();
                //     if($check){
                //         $c->update([
                //             'program_id'=> $check->program_id,
                //         ]);
                //     }

                // }
                $resolvedComplains =  Complain::where('status', '=', 'Resolved')->count();
                $pendingComplains =  Complain::where('status', '=', 'Pending')->count();
                $InProgressComplains =  Complain::where('status', '=', 'In Progress')->count();
            }

            return view('dashboard.admin.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains'));
        } elseif (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {

            $program = Program::find($request->p_id);

            $resolvedComplains =  Complain::where(['user_id' => Auth::user()->id, 'status' => 'Resolved', 'program_id' => $request->p_id])->count();
            $pendingComplains =  Complain::where(['user_id' => Auth::user()->id, 'status' => 'Pending', 'program_id' => $request->p_id])->count();
            $InProgressComplains =  Complain::where(['user_id' => Auth::user()->id, 'status' => 'In Progress', 'program_id' => $request->p_id])->count();
            $complains = Complain::where(['user_id' => Auth::user()->id, 'program_id' => $request->p_id])->orderBy('created_at', 'DESC')->get();

            return view('dashboard.student.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains', 'program'));
        } else return back();
    }

    public function create(Request $request)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
                $programs = Auth::user()->trainings;
                $programs = $programs->map(function ($q) {
                    $q->p_name = Program::where('id', $q->program_id)->value('p_name');
                    return $q;
                });
            } else {
                $programs = Program::where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
            }
            $program = '';

            return view('dashboard.admin.complains.create')
                ->with('extend', 'dashboard.admin.index')
                ->with('programs', $programs);
        } elseif (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            $program = Program::find($request->p_id);

            return view('dashboard.admin.complains.create')->with('extend', 'dashboard.student.trainingsindex')->with('program', $program);
        }
        return back();
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
            'issues' => 'required',
            'priority' => 'required',
            'status' => 'required',
            'gender' => 'required',
            'teamlead' => 'nullable',
            'complain' => 'required',
            'other' => 'nullable',
            'response' => 'nullable',
            'notes' => 'nullable',
            'program_id' => 'nullable',
        ]);
        // dd($data);
        if (!empty($data['notes'])) {
            $data['notes'] =  $data['notes'];
        } else {
            $data['notes'] = 0;
        }

        if ($data['type'] == "Enquiry") {
            $sla = 0;
        } else $sla = rand(4, 6);
        // dd($request->all(), $request->program_id);

        $query = Complain::create([
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
            'issues' => $data['issues'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'gender' => $data['gender'],
            'teamlead' => $data['teamlead'],
            'notes' => $data['notes'],
            'content' => $data['complain'],
            'response' => $data['response'],
            'program_id' => $data['response'],
            'sla' => $sla,
            'program_id' =>  $data['program_id'],

        ]);
        return back()->with('message', 'CRM has been created succesfully');
    }

    public function show(Complain $complain)
    {
    }

    public function edit(Complain $complain, Request $request)
    {

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.admin.index');
        } elseif (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            $program = Program::find($request->p_id);
            return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.student.trainingsindex')->with('program', $program);
        }
        return back();
    }

    public function update(Complain $complain, Request $request)
    {
        // dd($request->all());
        $data = $request->except(['p_id']);
        $complain->update($data);

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

    private function percentage($id)
    {
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
