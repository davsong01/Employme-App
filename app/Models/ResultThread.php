<?php

namespace App\Models;

use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ResultThread extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    protected $table = 'result_threads';
    
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function getName($pid)
    {
        $p_name = DB::table('program')->where('id', $pid)->value('p_name');
        return $p_name;
    }

}
