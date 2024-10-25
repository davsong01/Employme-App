<?php

namespace App\Imports;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Validator;


class QuestionsImport implements ToModel, WithHeadingRow
{
    use Importable;

    protected $p_id;

    function __construct($p_id) {
        $this->p_id = $p_id;
    }


    public function model(array $row)
    {
        Validator::make($row,
			[
				'title'=> 'required|unique:questions,title',
				'module'=> 'required|exists:modules,title',
                'optiona' => 'nullable',
                'optionb' => 'nullable',
                'optionc' => 'nullable',
                'optiond' => 'nullable',
                'correct' => 'nullable',
			],
			[
                'title.required' => 'One or more questions do not have a title, please check and try again',
                'title.unique' => 'One or more Questions already exists in database, please check and try again',
				
				'module.exists' => 'One or more Modules do not exist in the database, please check the module column and try again',
                'optiona.required' => 'One or more rows require option A, Please check and try again',
                'optionb.required' => 'One or more rows require option B, Please check and try again',
                'optionc.required' => 'One or more rows require option C, Please check and try again',
                'optiond.required' => 'One or more rows require option D, Please check and try again',
                'correct.required' => 'One or more rows require the correct option, Please check and try again',
			
			]
            )->validate();
           
        //get module ID
        $module_id = $this->getModuleId(trim($row['module']));

        //take care of nullable fields
        $optiona = isset($row['optiona'])? trim($row['optiona']) : null;
        $optiona = isset($row['optiona'])? trim($row['optiona']) : null;
				

        return new Question([
            'title' => trim($row['title']),
            'module_id' => $module_id,
            'optionA' => $optiona,
            'optionB' => trim($row['optionb']),
            'optionC' => trim($row['optionc']),
            'optionD' => trim($row['optiond']),
            'correct' => trim($row['correct']),
        ]);

    }

    private function getModuleId($module){
        $moduleId = DB::table('modules')->where('title', $module)->value('id');
        if(!$moduleId){
            dd('One or more module(s) in the uploaded file has not been added to the module Record, add all modules first and try again');
        }
        return $moduleId;
    }
}
