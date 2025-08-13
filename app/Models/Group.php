<?php

namespace App\Models;

use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory;

    /* -----------------------------------------------------------------
    |  Mass-assignment & casting
    |------------------------------------------------------------------*/
    protected $guarded = [];

    protected $casts = [
        'currencies' => 'array',
        'meta'       => 'array',
    ];

    /* -----------------------------------------------------------------
    |  Relationships
    |------------------------------------------------------------------*/

    /** The parent program used to *create* this package. */
    public function parentProgram()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    /** Programs bundled inside this package. */
    public function programs()
    {
        return $this->belongsToMany(Program::class, 'group_program')->withTimestamps();
    }

    public function scopeIsActive($query)
    {
        return $query->where('groups.status', 1)
            // ->where('groups.p_end', '>=', date('Y-m-d'))
            ->whereHas('programs', function ($q) {
                $q->mainActivePrograms();
            });
    }
    

    // Fetch a package with its programs
    // $bundle = Group::with('programs')->find($id);

    // // Get all bundles a learner is buying
    // $bundles = Group::whereKey($selectedGroupIds)->with('programs')->get();

    // // Total at checkout
    // $total = $bundles->sum('price');
    
    // return $this->belongsToMany(Program::class, 'group_programs')
    //     ->using(GroupProgram::class)
    //     ->withTimestamps();
}
