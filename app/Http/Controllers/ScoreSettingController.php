<?php

namespace App\Http\Controllers;

use App\Module;
use App\Program;
use App\ScoreSetting;
use Illuminate\Http\Request;

class ScoreSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $i = 1;
       
        if(Auth()->user()->role_id == "Admin"){
            $scores = ScoreSetting::orderBy('program_id', 'DESC')->get();
            
            foreach($scores as $score){
                $score['module_count'] = 0;
                $score['module_status_count'] = 0;

                if(!isset($score->program->module)){ $score['module_count'] = 0; };

                foreach($score->program->modules as $modules){
                    if($modules->status == 1){
                        $score['module_status_count'] += 1;
                    }
                }
                
            }       
            
            return view('dashboard.admin.scoresettings.index', compact('scores', 'i') );
        }
          
          elseif(Auth()->user()->role_id == "Teacher"){
              $scores = ScoreSetting::where('program_id', '=', Auth::user()->program_id )->orderBy('program_id', 'DESC')->get();
              return view('dashboard.admin.scoresettings.index', compact('scores', 'i') );
            }
            
            return redirect('/dashboard');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(auth()->user()->role_id == "Admin"){
            $programs = Program::with(['scoresettings', 'modules'])->where('id', '<>', '1')->orderBy('created_at', 'DESC')->get();
        foreach($programs as $program){
            
            $program['counter'] = 0;
            if(isset($program->scoresettings)){ $program['settings_count'] = 1; }else $program['settings_count'] = 0;

            //check if any of program's module is enabled
            foreach($program->modules as $module){
                
                if($module->status == 1){
                    $program['counter'] +=1;
                };
            }  
        }
        
            return view('dashboard.admin.scoresettings.create', compact('programs'));
        }

        elseif(auth()->user()->role_id == "Teacher"){
            $programs = Program::where('id', '=', auth()->user()->program_id)->where('id', '<>', '1')->get();
            $users = User::where('role_id', '=', "Student")->where('hasResult', '<>', 1)->where('program_id', '=', Auth::user()->program_id)->orderBy('created_at', 'DESC')->get();
                //return view('dashboard.admin.results.create', compact('users', 'programs'));
                }else
                    return redirect('/dashboard');
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'program' => 'required|numeric',
            'classtests' => 'required|numeric|min:1|max:100',
            'rolepalyscore' => 'required|numeric|min:1|max:100',
            'emailscore' => 'required|numeric|min:1|max:100',
            'certificationscore' => 'required|numeric|min:1|max:100',
            'passmark' => 'required|numeric|min:1|max:100',
        ]);

        $total = array_sum(array_except($data, ['passmark', 'program']));

        if($total > 100 || $total < 100){
            return back()->with('error', 'Sorry, sum of parameters cannot be more than or less than 100%, please try again');
        }

        ScoreSetting::create([
            'program_id' => $data['program'],
            'certification' => $data['certificationscore'],
            'class_test' => $data['classtests'],
            'role_play' => $data['rolepalyscore'],
            'email' => $data['emailscore'],
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
            'classtests' => 'required|numeric|min:1|max:100',
            'rolepalyscore' => 'required|numeric|min:1|max:100',
            'emailscore' => 'required|numeric|min:1|max:100',
            'certificationscore' => 'required|numeric|min:1|max:100',
            'passmark' => 'required|numeric|min:1|max:100',
        ]);

        
        $total = array_sum(array_except($data, ['passmark', 'program']));
        
        if($total > 100 || $total < 100){
            return back()->with('error', 'Sorry, sum of parameters cannot be more thanor less than 100, please try again');
        }
       
        $scoreSetting->program_id = $request['program'];
        $scoreSetting->certification = $request['certificationscore'];
        $scoreSetting->class_test = $request['classtests'];
        $scoreSetting->role_play = $request['rolepalyscore'];
        $scoreSetting->email = $request['emailscore'];
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
