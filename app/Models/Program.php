<?php

namespace App\Models;
use App\Models\User;
use DateTime;
use App\Models\Mocks;
use App\Models\Coupon;
use App\Models\Module;
use App\Models\Result;
use DatePeriod;
use App\Models\Location;
use App\Models\Material;
use DateInterval;
use App\Models\Certificate;
use App\Models\ScoreSetting;
use App\Models\FacilitatorTraining;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    protected $casts = ['auto_certificate_settings' => 'array'];
    
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

    public function getPriceRangeAttribute()
    {
        if ($this->subPrograms->isEmpty()) {
            return [];
        }

        $amounts = new Collection([$this->p_amount]);

        if ($this->subPrograms->isNotEmpty()) {
            $amounts = $this->subPrograms->pluck('p_amount');
            // $amounts = $amounts->merge($this->subPrograms->pluck('p_amount'));
        }

        $from = $amounts->min();
        $to = $amounts->max();

        return [
            'from' => $from,
            'to' => $to
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Program::class, 'parent_id');
    }

    public function children()
    {
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

    public function scopeAllMainPrograms($query)
    {
        return $query->where('id', '<>', 1)
        ->whereNULL('parent_id')
        ->whereStatus(1)
            // ->where('p_end', '>=', date('Y-m-d'))
            ->where('close_registration', 0)
            ->orderBy('created_at', 'DESC');
    }

    public function scopeActivePrograms($query)
    {
        return $query->where('id', '<>', 1)
            ->whereStatus(1)
                ->where('p_end', '>=', date('Y-m-d'))
                    ->where('close_registration', 0)
                        ->orderBy('created_at', 'DESC');
    }

    public function coupon()
    {
        return $this->hasMany(Coupon::class);
    } 

    public function programRange(){
        $start    = (new DateTime($this->p_start))->modify('first day of this month');
        $end      = (new DateTime($this->p_end))->modify('first day of this month');
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start, $interval, $end);

        $months = array();

        foreach ($period as $dt) {
            if(!in_array($dt->format("F"), ['May','June','October','November'])){
                $months[] = $dt->format("F Y");
            }
        }
        return $months;
    }
}
