<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\Program;
use App\Models\Transaction;
use App\Models\ScoreSetting;
use Illuminate\Http\Request;

class ScoreSettingController extends Controller
{
    public function index()
    {
        $i = 1;
        if (checkRoleHas(['Admin'])) {
            $scores = ScoreSetting::orderBy('program_id', 'DESC')->get();
        }else{
            $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
            $scores = ScoreSetting::orderBy('program_id', 'DESC')->whereIn('program_id', $trainings)->orderBy('id', 'desc')->get();
        }

        foreach ($scores as $score) {
            $score['module_count'] = 0;
            $score['module_status_count'] = 0;

            if (!isset($score->program->module)) {
                $score['module_count'] = 0;
            };

            if (isset($score->program->modules)) {
                foreach ($score->program->modules as $modules) {
                    if ($modules->status == 1) {
                        $score['module_status_count'] += 1;
                    }
                }
            }
        }

        return view('dashboard.admin.scoresettings.index', compact('scores', 'i'));
    }

    public function create()
    {
        if (checkRoleHas(['Facilitator', 'Admin'])) {
            $programs = Program::withCount(['scoresettings', 'modules'])->where('id', '<>', '1')->orderBy('created_at', 'DESC')->get();
            
            return view('dashboard.admin.scoresettings.create', compact('programs'));
        }

        if (checkRoleHas(['Facilitator','Admin'])) {
            $programs = Program::with(['scoresettings', 'modules'])->where('id', '<>', '1')->where('id', resolveAuthUser()->program->id)->orderBy('created_at', 'DESC')->get();
            foreach ($programs as $program) {
                $program['counter'] = 0;
                if (isset($program->scoresettings)) {
                    $program['settings_count'] = 1;
                } else $program['settings_count'] = 0;

                //check if any of program's module is enabled
                foreach ($program->modules as $module) {

                    if ($module->status == 1) {
                        $program['counter'] += 1;
                    };
                }
            }

            return view('dashboard.admin.scoresettings.create', compact('programs'));
        }
        
        return redirect('/dashboard');
    }

    public function store(Request $request)
    {

        $data = $this->validate($request, [
            'program' => 'required|numeric',
            'passmark' => 'required|numeric|min:1|max:100',
            // 'program' => 'required|numeric',
            // 'classtests' => 'nullable|numeric|min:1|max:100',
            // 'roleplayscore' => 'nullable|numeric|min:1|max:100',
            // 'emailscore' => 'nullable|numeric|min:1|max:100',
            // 'certificationscore' => 'nullable|numeric|min:1|max:100',
            // 'passmark' => 'required|numeric|min:1|max:100',
        ]);

        $total = array_sum(array_map('intval', array_filter($request->except(['passmark', 'program', '_token', 'submit', 'prefix__']), function ($value) {
            return $value !== null;
        })));
        
        if ($total > 100 || $total < 100) {
            return back()->with('error', 'Sorry, sum of parameters cannot be more than or less than 100%, please try again');
        }

        ScoreSetting::create([
            'program_id' => $data['program'],
            'certification' => $data['certificationscore'] ?? 0,
            'class_test' => $data['classtests'] ?? 0,
            'role_play' => $data['rolepalyscore'] ?? 0,
            'crm_test' => $data['crm_test'] ?? 0,
            'email' => $data['emailscore'] ?? 0,
            'passmark' => $data['passmark'],
            'total' => $total,
        ]);

        return redirect(route('scoreSettings.index'))->with('message', 'Score Setting succesfully Defined');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ScoreSetting  $scoreSetting
     * @return \Illuminate\Http\Response
     */
    public function show(ScoreSetting $scoreSetting)
    {
        //
    }


    public function edit(ScoreSetting $scoreSetting)
    {
        return view('dashboard.admin.scoresettings.edit', compact('scoreSetting'));
    }


    public function update(Request $request, ScoreSetting $scoreSetting)
    {
        // dd($request->all());
        $data = $this->validate($request, [
            'program' => 'required|numeric',
            'passmark' => 'required|numeric|min:1|max:100',
            // 'classtests' => 'sometimes|numeric|min:1|max:100',
            // 'rolepalyscore' => 'sometimes|numeric|min:1|max:100',
            // 'emailscore' => 'sometimes|numeric|min:1|max:100',
            // 'certificationscore' => 'sometimes|numeric|min:1|max:100',
        ]);
       
        $total = array_sum(array_map('intval', array_filter($request->except(['passmark', 'program', '_token', 'submit', 'prefix__']), function ($value) {
            return $value !== null;
        })));
        
        if ($total > 100 || $total < 100) {
            return back()->with('error', 'Sorry, sum of parameters cannot be more than or less than 100, please try again');
        }

        $scoreSetting->program_id = $request['program'];
        $scoreSetting->certification = $request['certificationscore'] ?? 0;
        $scoreSetting->class_test = $request['classtests'] ?? 0;
        $scoreSetting->role_play = $request['roleplayscore'] ?? 0;
        $scoreSetting->email = $request['emailscore'] ?? 0;
        $scoreSetting->crm_test = $request['crm_test'] ?? 0;
        $scoreSetting->passmark = $request['passmark'];
        $scoreSetting->total = $total;

        $scoreSetting->save();

        if(!empty($request->update_previous_score) && $request->update_previous_score == "on"){
            $this->updatePreviousScores($scoreSetting);
        }
        return redirect(route('scoreSettings.index'))->with('message', 'setting updated successfully');
    }

    public function updatePreviousScores($scoreSetting){
        return;
        $programId = $scoreSetting->program_id;
        $transactions = Transaction::with('program', 'program.scoresettings')->where('program_id', $programId)->get();
        
        foreach($transactions as $transaction){
            $result = Transaction::with('program', 'program.scoresettings')->where('id', $id)->first();
            $realResult = Result::where('id', $request->real_result_id)->first();

            $request["email_test_score"] = $request->emailscore;
            $request["roleplay_test_score"] = $request->roleplayscore;
            $request["crm_test_score"] = $request->crm_score;
            $request["certification_test_score"] = $request->certification_score;

            $request["certification_facilitator_comment"] = $request->facilitator_comment;

            $request["certification_grader"] = ($request->certification_score <> $result->training_result->certification_test_score) ? resolveAuthUser()->name : $result->training_result->certification_grader;

            $request["certification_facilitator"] = ($request->roleplayscore <> $result->training_result->roleplay_test_score) ? resolveAuthUser()->name : $result->training_result->certification_facilitator;

            $request["certification_grader_comment"] = $request->grader_comment;

            $transaction = udateTrainingResult($result->program_id, $result->user_id, $request->all());

            $realResult->update([
                "email_test_score" => $request->emailscore,
                "role_play_score" => $request->roleplayscore,
                "certification_test_score" => $request->certification_score,
                "crm_test_score" => $request->crm_score,
                "grader_comment" => $request->grader_comment,
                "facilitator_comment" => $request->facilitator_comment,
                "grader" => resolveAuthUser()->name,
                "marked_by" => resolveAuthUser()->name,
            ]);
        }
        udateTrainingResult($program_id, $user_id, $data = []);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScoreSetting  $scoreSetting
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScoreSetting $scoreSetting)
    {
        $scoreSetting->delete();

        return back()->with('message', 'delete successful!');
    }
}
