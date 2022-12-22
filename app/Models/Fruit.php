<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fruit extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'slug',
        'meta',
    ];

    // protected $casts = [
    //     'children'        => 'object'
    // ];

    public function getMetaAttribute($value){
        return json_decode($value, true);
    }

    public function children()
    {
        return $this->hasMany(Child::class)->where('index', 1)->orderBy('label');
    }
}
