<?php

namespace App;
use App\User;
use App\Mocks;
use App\Coupon;
use App\Module;
use App\Result;
use App\Location;
use App\Material;
use App\Certificate;
use App\ScoreSetting;
use App\FacilitatorTraining;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
  
    public function scoresettings(){
        return $this->hasOne(ScoreSetting::class, 'program_id');
    }

    public function locations(){
        return $this->hasMany(Location::class);
    }

    public function users(){
        return $this->belongsToMany(User::class)->withPivot('t_amount', 'invoice_id', 'balance', 'transid');
    }

    //Create relationship between this model and the materials model
    public function materials(){
        return $this->hasMany(Material::class);
    }

    public function results(){
        return $this->hasMany(Result::class);
    }

    public function mocks(){
        return $this->hasMany(Mocks::class);
    }
    
    public function modules(){
        return $this->hasMany(Module::class);
    }

    public function certificates(){
        return $this->hasMany(Certificate::class, 'program_id');
    }
    
    public function questions()
    {
        return $this->hasManyThrough('App\Question', 'App\Module');
    }

    //Facilitator's relationship
    public function trainings()
    {
        return $this->hasManyThrough(FacilitatorTraining::class);
    }
    
    public function checkBalance($p_id)
    {
        $balance = DB::table('program_user')->where('user_id', auth()->user()->id)->where('program_id', $p_id)->value('balance');
        return $balance;
    }
    
    public function subPrograms(){
        return $this->hasMany(Program::class, 'parent_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', \Carbon\Carbon::today());
    }

    public function scopeMainActivePrograms($query){
        return $query->where('id', '<>', 1)
            ->whereNULL('parent_id')
            ->whereStatus(1)
            ->where('p_end', '>=', date('Y-m-d'))
            ->where('close_registration', 0)
            ->orderBy('created_at', 'DESC');
    }

    public function coupon()
    {
        return $this->hasMany(Coupon::class);
    } 
}
