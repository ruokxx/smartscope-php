<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
  protected $fillable = [
    'user_id', 'object_id', 'scope_id', 'filename', 'path', 'upload_time',
    'exposure_total_seconds', 'sub_exposure_seconds', 'sub_exposure_time', 'number_of_subs',
    'iso_or_gain', 'gain', 'filter', 'bortle', 'seeing', 'session_date',
    'processing_software', 'processing_steps', 'notes', 'approved', 'disk'
  ];

  // Accessor for full URL
  public function getUrlAttribute()
  {
    // If disk is set, use it. Default to public.
    $disk = $this->disk ?: 'public';

    // If using S3/FTP, get URL from driver. 
    // Note: FTP might not support url() method out of the box unless configured? 
    // S3 definitely does.
    return \Illuminate\Support\Facades\Storage::disk($disk)->url($this->path);
  }

  // cast upload_time to a Carbon instance
  protected $casts = [
    'upload_time' => 'datetime',
    'session_date' => 'date',
  ];

  public function user()
  {
    return $this->belongsTo(\App\Models\User::class);
  }
  public function object()
  {
    return $this->belongsTo(\App\Models\Obj::class , 'object_id');
  }
  public function scopeModel()
  {
    return $this->belongsTo(\App\Models\Scope::class , 'scope_id');
  }
}
