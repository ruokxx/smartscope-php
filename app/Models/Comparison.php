<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Comparison extends Model
{
    protected $fillable = ['object_id','image_id_dwarf','image_id_seestar','created_by','notes'];
}
