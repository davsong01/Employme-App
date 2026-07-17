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

        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return back();
        }

        $query = Complain::with(['user', 'program'])->where('program_id', $p_id->id);

        if (checkRoleHas(['Admin'])) {
            // Admin sees all complaints for this program
            $complains = $query->orderBy('user_id', 'DESC')->get();
        } else {
            // Facilitator or Grader sees only complaints for their assigned trainings
            $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
            $complains = $query->whereIn('program_id', $trainings)->orderBy('user_id', 'DESC')->get();
        }

        // Filter counts by the same program
        $complainCounts = Complain::where('program_id', $p_id->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $resolvedComplains   = $complainCounts->get('Resolved', 0);
        $pendingComplains    = $complainCounts->get('Pending', 0);
        $inProgressComplains = $complainCounts->get('In Progress', 0);

        return view('dashboard.admin.complains.index', [
            'complains'           => $complains,
            'training'            => $p_id,
            'i'                   => $i ?? null,
            'resolvedComplains'   => $resolvedComplains,
            'pendingComplains'    => $pendingComplains,
            'InProgressComplains' => $inProgressComplains,
        ]);
    }

    
    public function create(Request $request)
    {
        $training = Program::where('id', request()->p_id)->first();
        if (!$training) {
            return back()->with('error', 'Please select a valid training before logging a case.');
        }

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
        $data = $request->validate($this->storeRules());

        $training = Program::where('id', $data['program_id'])->first();
        if (!$training) {
            return back()->with('error', 'Please select a valid training before logging a case.');
        }

        if($training->hascrm == 0){
            return back()->with('error', 'CRM not enabled for: '.$training->p_name);
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
            'subject' => $data['subject'] ?? null,
            'category' => $data['category'] ?? null,
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
            'content' => $data['content'] ?? null,
            'response' => $data['response'] ?? null,
            'follow_up_at' => $data['follow_up_at'] ?? null,
            'tags' => $data['tags'] ?? null,
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
        $data = $request->validate($this->updateRules());

        $complain->update($data);
        //Update User Percentage Response
        if (request()->prefix__ != '/admin') {
            //Update User Percentage Response
            $this->percentage($complain->user_id);
        }
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

        if(request()->prefix__ != '/admin'){
            //Update User Percentage Response
            $this->percentage($complain->user_id);
        }

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

    private function storeRules(): array
    {
        return [
            'name' => 'required|string|min:5|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'subject' => 'nullable|string|max:160',
            'category' => 'nullable|string|max:120',
            'state' => 'required|string|max:80',
            'lga' => 'required|string|max:120',
            'address' => 'required|string|min:5|max:160',
            'mode' => 'required|string|max:40',
            'type' => 'required|string|max:40',
            'issues' => 'required|string|max:120',
            'priority' => 'required|string|max:20',
            'status' => 'required|string|max:30',
            'gender' => 'required|string|max:20',
            'teamlead' => 'nullable|string|max:120',
            'content' => 'required|string',
            'other' => 'nullable|string|max:160',
            'response' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_at' => 'nullable|date',
            'tags' => 'nullable|string|max:255',
            'program_id' => 'required|exists:programs,id',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => 'nullable|string|min:5|max:50',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:160',
            'category' => 'nullable|string|max:120',
            'state' => 'nullable|string|max:80',
            'lga' => 'nullable|string|max:120',
            'address' => 'nullable|string|min:5|max:160',
            'mode' => 'nullable|string|max:40',
            'type' => 'required|string|max:40',
            'issues' => 'required|string|max:120',
            'priority' => 'required|string|max:20',
            'status' => 'required|string|max:30',
            'gender' => 'nullable|string|max:20',
            'teamlead' => 'nullable|string|max:120',
            'content' => 'required|string',
            'other' => 'nullable|string|max:160',
            'response' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_at' => 'nullable|date',
            'tags' => 'nullable|string|max:255',
        ];
    }
}
