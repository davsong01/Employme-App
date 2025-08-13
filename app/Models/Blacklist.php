<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function addedBy(){
        return $this->belongsTo(Admin::class, 'added_by');
    }

    public static function isBlacklisted(string $type, string $value): bool
    {
        return self::where('value', $value)->where('status', 1)
            ->exists();
    }
}
