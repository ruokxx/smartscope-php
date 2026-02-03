<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
      'user_id','object_id','scope_id','filename','path','upload_time',
      'exposure_total_seconds','sub_exposure_seconds','number_of_subs',
      'iso_or_gain','filter','processing_software','processing_steps','notes','approved'
    ];

    // cast upload_time to a Carbon instance
    protected $casts = [
        'upload_time' => 'datetime',
    ];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function object() { return $this->belongsTo(\App\Models\Obj::class, 'object_id'); }
    public function scopeModel() { return $this->belongsTo(\App\Models\Scope::class, 'scope_id'); }
}
