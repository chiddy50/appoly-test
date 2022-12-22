<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Child;
use Illuminate\Support\Facades\DB;

class Child extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'slug',
        'parent_id',
        'index',
        'fruit_id',
        'meta'
    ];

    protected $appends = [
        // 'children_data'
    ];

    public function getMetaAttribute($value){
        return json_decode($value, true);
    }

    public function fruit()
    {
        return $this->belongsTo(Fruit::class);
    }

    // public function subData()
    // {
    //     return $this->hasMany(Child::class, 'parent_id');
    // }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('label');
    }

    // public function parent()
    // {
    //     return $this->belongsTo(Child::class, 'parent_id');
    // }

    // public function children()
    // {
    //     return $this->hasMany(Child::class, 'parent_id');
    // }

    // public function childrenRecursive()
    // {
    // return $this->children()->with('childrenRecursive');
    // }


}
