<?php

namespace App;
use App\User;
use App\Module;
use App\Result;
use App\Material;
use App\ScoreSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    public function scoresettings(){
        return $this->hasOne(ScoreSetting::class);
    }
    //Create relationship between this model and the students model
    public function users(){
        return $this->belongsToMany(User::class);
    }

    // public function users(){
    //     return $this->hasMany(User::class);
    // }

    //Create relationship between this model and the materials model
    public function materials(){
        return $this->hasMany(Material::class);
    }

    public function results(){
        return $this->hasMany(Result::class);
    }
    
    public function modules(){
        return $this->hasMany(Module::class);
    }

    public function questions()
    {
        return $this->hasManyThrough('App\Question', 'App\Module');
    }
    
}
