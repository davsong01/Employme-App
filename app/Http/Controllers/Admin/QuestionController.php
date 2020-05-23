<?php

namespace App\Http\Controllers\Admin;

use App\Module;
use App\Program;
use App\Complain;
use App\Material;
use App\Question;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{

    public function index()
    {
        if(Auth::user()->role_id == "Admin"){
            $i = 1;  
            $questions = Question::with( 'module' )->orderBy('id', 'DECS')->get();
            return view('dashboard.admin.questions.index', compact('questions', 'i'));
        } return back();
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $modules = Module::all();
            return view('dashboard.admin.questions.create', compact('modules'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'module' => 'required|numeric',
            'title' => 'required',
            'optionA' => 'sometimes',
            'optionB' => 'sometimes',
            'optionC' => 'sometimes',
            'optionD' => 'sometimes',
            'correct' => 'sometimes',
        ]);


        //get module
        $module = Module::findorFail($data['module']);
        
        //Check if options are needed or not for this question
        if($this->checkOptions($data, $module) == 0){
            return back()->with('error', 'No options needed for the associated module, please remove all options and try again');
        }

        if($this->checkOptions($data, $module) == 2){
            return back()->with('error', 'The associated module needs options, please enter all options and try again');
        }
        
        //check if module already has maximum number of questions
        if($module->questions->count() >= $module->noofquestions ){
            return back()->with('error', 'The associated module already has maximum number of questions'); 
        }
        $question = Question::create([
            'title' => $request->title,
            'optionA' => $request->optionA,
            'optionB' => $request->optionB,
            'optionC' => $request->optionC,
            'optionD' => $request->optionD,
            'correct' => $request->correct,
            'module_id' => $request->module
        ]);

        return redirect('questions')->with('message', 'Question succesfully added');
    }
    public function show(Question $question)
    {
        //
    }

    public function edit(Question $question)
    {
        $modules = Module::orderBy('created_at', 'DESC')->get();
        return view('dashboard.admin.questions.edit', compact('question', 'modules'));
    }

    public function update(Request $request, Question $question)
    {
        $question->update($request->all());
        return redirect('questions')->with('message', 'Question has been succesfully updated');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('message', 'Question has been deleted successfully');

    }

    private function checkOptions($values, $module){
        if ( $module->type == 'Open Ended'){
            if(!empty($values['optionA']) || !empty($values['optionB']) && !empty($values['optionC']) || !empty($values['optionD']) || !empty($values['correct'])){
                return 0; //No options needed for the associated module
            }else{
                return 1; //Everything is fine, continue
            }
        }
    
        if( $module->type == 'Multi Choice' && (empty($values['optionA']) || empty($values['optionB']) && empty($values['optionC']) || empty($values['optionD']) || empty($values['correct']))){
            return 2; //The associated module needs options
        } else{
            return 1; //Everything is fine, continue
        }
    }
}
