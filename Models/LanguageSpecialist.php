<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSpecialist extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function languages()
    {
        return $this->belongsTo(language::class, 'language_id');
    }
}
