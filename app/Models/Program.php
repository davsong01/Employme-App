<?php

namespace App\Models;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\User;
use App\Models\Group;
use App\Models\Mocks;
use App\Models\Coupon;
use App\Models\Module;
use App\Models\Result;
use App\Models\Complain;
use App\Models\Location;
use App\Models\Material;
use App\Models\Certificate;
use App\Models\Transaction;
use App\Models\ScoreSetting;
use App\Models\CertificateProgram;
use App\Models\CertificateTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\FacilitatorTraining;
use Illuminate\Database\Eloquent\Model;
use App\Services\CurrencyAmountFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CertificateRegenerationTemplate;

class Program extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];
    protected $casts = [
        'modes' => 'array',
        'auto_certificate_settings' => 'array', 
        'currencies' => 'array', 
        'ai_settings' => 'array'
        ];
    
    public function scoresettings(){
        return $this->hasOne(ScoreSetting::class, 'program_id');
    }

    public function locations(){
        return $this->hasMany(Location::class);
    }

    public function users(){
        return $this->belongsToMany(User::class)->withPivot('amount', 'invoice_id', 'balance', 'transid');
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
        return $this->hasManyThrough('App\Models\Question', 'App\Models\Module');
    }

    //Facilitator's relationship
    // public function trainings()
    // {
    //     return $this->hasManyThrough(FacilitatorTraining::class);
    // }

    public function trainings()
    {
        return $this->hasMany(FacilitatorTraining::class, 'program_id');
    }

    public function crm()
    {
        return $this->hasMany(Complain::class, 'program_id');
    }

    public function training()
    {
        return $this->hasOne(FacilitatorTraining::class, 'program_id');
    }

    
    public function checkBalance($p_id)
    {
        $balance = DB::table('program_user')->where('user_id', resolveAuthUser()->id)->where('program_id', $p_id)->value('balance');
        return $balance;
    }
    
    public function subPrograms(){
        return $this->hasMany(Program::class, 'parent_id');
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

    // public function scopeMainActivePrograms($query){
    //     return $query->where('id', '<>', 1)
    //         ->whereNULL('parent_id')
    //         ->whereStatus(1)
    //         ->where('p_end', '>=', date('Y-m-d'))
    //         ->where('close_registration', 0)
    //         ->orderBy('created_at', 'DESC');
    // }
    
    public function scopeMainActivePrograms($query)
    {
        return $query->where('programs.id', '<>', 1)
            // ->whereNull('programs.parent_id')
            ->where('programs.status', 1)
            ->where('programs.is_closed', 'no')
            ->where('programs.p_end', '>=', date('Y-m-d'))
            ->where('programs.close_registration', 0)
            ->orderBy('programs.created_at', 'DESC');
    }

    public function scopeAllMainPrograms($query)
    {
        return $query->where('programs.id', '<>', 1)
        ->whereStatus(1)
            // ->where('programs.close_registration', 0)
            ->orderBy('programs.created_at', 'DESC');
    }


    public function scopeMainActiveProgramsWithIsClosed($query)
    {
        return $query->where('programs.id', '<>', 1)
            // ->whereNull('programs.parent_id')
            ->where('programs.status', 1)
            ->where('programs.p_end', '>=', date('Y-m-d'))
            ->where('programs.close_registration', 0)
            ->orderBy('programs.created_at', 'DESC');
    }

    public function scopeisArchived($query)
    {
        return $query->where('is_archived', 1);
    }

    public function scopeisNotArchived($query)
    {
        return $query->where('is_archived', 0);
    }


    public function scopeActivePrograms($query)
    {
        return $query->where('id', '<>', 1)
            ->whereStatus(1)
                ->where('p_end', '>=', date('Y-m-d'))
                    ->where('close_registration', 0)
                        ->orderBy('created_at', 'DESC');
    }

    public function scopeIsUserProgram($query)
    {
        return $query->where('id', '<>', 1)
        // ->whereStatus(1)
            ->where('program_lock', 0);
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

    public function fullyPaid()
    {
        return $this->hasMany(Transaction::class, 'program_id')->where('balance', '<=', 0);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_program')->withTimestamps();
    }

    // public function regenerationTemplates()
    // {
    //     return $this->belongsToMany(CertificateRegenerationTemplate::class, 'certificate_programs', 'program_id', 'certificate_regeneration_id');
    // }
    public function regenerationTemplate()
    {
        return $this->hasOneThrough(
            CertificateRegenerationTemplate::class,
            CertificateProgram::class,
            'program_id',                      // Foreign key on certificate_programs table
            'id',                              // Foreign key on certificate_regeneration_templates table
            'id',                              // Local key on programs table
            'certificate_regeneration_id'      // Local key on certificate_programs table
        );
    }

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function getSlugAttribute($value)
    {
        // Remove invisible characters
        return trim(preg_replace('/[\x{2060}\x{FEFF}]/u', '', $value));
    }

    public function scopeWithPaymentStats($query)
    {
        return $query
            ->select('*')
            ->selectRaw("
                (
                    SELECT COUNT(*)
                    FROM temp_transactions tt
                    WHERE tt.status='complete'
                    AND tt.balance=0
                    AND JSON_CONTAINS(tt.program_ids, JSON_ARRAY(programs.id))
                ) as fully_paid
            ")
            ->selectRaw("
                (
                    SELECT COUNT(*)
                    FROM temp_transactions tt
                    WHERE tt.status='complete'
                    AND tt.balance>0
                    AND JSON_CONTAINS(tt.program_ids, JSON_ARRAY(programs.id))
                ) as part_paid
            ");
    }
}
