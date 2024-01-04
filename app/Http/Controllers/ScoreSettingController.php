<?php

namespace App\Http\Controllers;

use App\Module;
use App\Program;
use App\ScoreSetting;
use Illuminate\Http\Request;

class ScoreSettingController extends Controller
{
    public function index()
    {
        $i = 1;

        if (Auth()->user()->role_id == "Admin") {
            $scores = ScoreSetting::orderBy('program_id', 'DESC')->get();

            foreach ($scores as $score) {
                $score['module_count'] = 0;
                $score['module_status_count'] = 0;

                if (!isset($score->program->module)) {
                    $score['module_count'] = 0;
                };

                foreach ($score->program->modules as $modules) {
                    if ($modules->status == 1) {
                        $score['module_status_count'] += 1;
                    }
                }
            }

            return view('dashboard.admin.scoresettings.index', compact('scores', 'i'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $scores = ScoreSetting::where('program_id', auth()->user()->program->id)->orderBy('program_id', 'DESC')->get();

            foreach ($scores as $score) {
                $score['module_count'] = 0;
                $score['module_status_count'] = 0;

                if (!isset($score->program->module)) {
                    $score['module_count'] = 0;
                };

                foreach ($score->program->modules as $modules) {
                    if ($modules->status == 1) {
                        $score['module_status_count'] += 1;
                    }
                }
            }

            return view('dashboard.admin.scoresettings.index', compact('scores', 'i'));
        }
        return redirect('/dashboard');
    }

    public function create()
    {
        if (auth()->user()->role_id == "Admin") {
            $programs = Program::withCount(['scoresettings', 'modules'])->where('id', '<>', '1')->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.scoresettings.create', compact('programs'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
            $programs = Program::with(['scoresettings', 'modules'])->where('id', '<>', '1')->where('id', auth()->user()->program->id)->orderBy('created_at', 'DESC')->get();
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

        $total = array_sum($request->except(['passmark', 'program', '_token']));


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


        $total = array_sum($request->except(['passmark', 'program', '_token']));

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

        return redirect(route('scoreSettings.index'))->with('message', 'setting updated successfully');
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
