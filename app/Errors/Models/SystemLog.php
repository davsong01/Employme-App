<?php 

namespace App\Errors\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'level',
        'message',
        'context',
        'stack_trace',
        'source',
        'logged_at'
    ];

    protected $casts = [
        'context' => 'array',
        'logged_at' => 'datetime',
    ];
}