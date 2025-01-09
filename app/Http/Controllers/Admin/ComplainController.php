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

        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            if (checkRoleHas(['Admin'])) {
                $trainings = Program::withCount('crm')->orderBy('created_at', 'desc')->get();
            }

            if (checkRoleHas(['Facilitator', 'Grader'])) {
                $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $trainings = Program::withCount('crm')->orderBy('created_at', 'desc')->whereIn('id', $trainings)->get();
            }
        
            return view('dashboard.admin.complains.selecttraining', compact('trainings','i'));
        } elseif (checkRoleHas(['Student'])){
            $program = Program::find($request->p_id);

            $resolvedComplains =  Complain::where(['user_id' => resolveAuthUser()->id, 'status' => 'Resolved', 'program_id' => $request->p_id])->count();
            $pendingComplains =  Complain::where(['user_id' => resolveAuthUser()->id, 'status' => 'Pending', 'program_id' => $request->p_id])->count();
            $InProgressComplains =  Complain::where(['user_id' => resolveAuthUser()->id, 'status' => 'In Progress', 'program_id' => $request->p_id])->count();
            $complains = Complain::where(['user_id' => resolveAuthUser()->id, 'program_id' => $request->p_id])->orderBy('created_at', 'DESC')->get();
            
            return view('dashboard.student.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains', 'program'));
        } else return back();
    }

    public function getTrainingCrm(Program $p_id){
        $i = 1;

        if (checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            if (checkRoleHas(['Admin'])) {
                $complains = Complain::where('program_id', $p_id->id)->with('user')->orderBy('user_id', 'DESC')->get();
            }

            if (checkRoleHas(['Facilitator', 'Grader'])) {
                $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $complains = Complain::with('user')->where('program_id', $p_id->id)->whereIn('program_id', $trainings)->orderBy('user_id', 'DESC')->get();
            }

            $complainCounts = Complain::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status');

            $resolvedComplains = $complainCounts->get('Resolved', 0);
            $pendingComplains = $complainCounts->get('Pending', 0);
            $InProgressComplains = $complainCounts->get('In Progress', 0);

            $training = $p_id;
            return view('dashboard.admin.complains.index', compact('complains', 'i', 'resolvedComplains', 'InProgressComplains', 'pendingComplains', 'training'));
        } else {
            return back();
        }
    }

    
    public function create(Request $request)
    {
        $training = Program::where('id', request()->p_id)->first();
        if (checkRoleHas(['Admin','Facilitator'])) {
            return view('dashboard.admin.complains.create')
                ->with('extend', 'dashboard.admin.index')
                ->with('training', $training);
        } elseif (checkRoleHas(['Student'])){

            return view('dashboard.admin.complains.create')->with('extend', 'dashboard.student.trainingsindex')
            ->with( 'training', $training)
                ->with('program', $training);
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
        
        $training = Program::where('id', $data['program_id'])->first();
        if($training->hascrm == 0){
            return back()->with('error', 'CRM not enabled for: '.$training->p_name);
        }

        if (!empty($data['notes'])) {
            $data['notes'] =  $data['notes'];
        } else {
            $data['notes'] = 0;
        }

        if ($data['type'] == "Enquiry") {
            $sla = 0;
        } else $sla = rand(4, 6);

        Complain::create([
            'user_id' => resolveAuthUser()->id,
            'name' => $data['name'] ?? null,
            'address' => $data['address'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'state' => $data['state'] ?? null,
            'lga' => $data['lga'] ?? null,
            'other' => $data['other'] ?? null,
            'mode' => $data['mode'] ?? null,
            'type' => $data['type'] ?? null,
            'issues' => $data['issues'] ?? null,
            'priority' => $data['priority'] ?? null,
            'status' => $data['status'] ?? null,
            'gender' => $data['gender'] ?? null,
            'teamlead' => $data['teamlead'] ?? null,
            'notes' => $data['notes'] ?? null,
            'content' => $data['complain'] ?? null,
            'response' => $data['response'] ?? null,
            'program_id' => $data['response'] ?? null,
            'sla' => $sla,
            'program_id' => $data['program_id'] ?? null,

        ]);

        return back()->with('message', 'Query Logged succesfully');
    }

    public function show(Complain $complain)
    {
    }

    public function edit(Complain $complain, Request $request)
    {
        if (checkRoleHas(['Admin','Facilitator'])) {
            return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.admin.index');
        } elseif (checkRoleHas(['Student'])){
            $program = Program::find($request->p_id);
            
            return view('dashboard.admin.complains.edit')->with('complain', $complain)->with('extend', 'dashboard.student.trainingsindex')->with('program', $program);
        }
        return back();
    }

    public function update(Complain $complain, Request $request)
    {
        // dd($request->all());
        $data = $request->except(['p_id', 'prefix__']);
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
