<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumbUsers extends Model
{
    use HasFactory;
    
    public $timestamps = true;
    
    public function scopeCheckEmail($emailadd) {
        return $query->where('email', $emailadd);
    }
}
