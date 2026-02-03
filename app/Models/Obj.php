<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Obj extends Model
{
    protected $table = 'objects';
    protected $fillable = ['name','catalog','ra','dec','type','description'];
    public function images() { return $this->hasMany(Image::class, 'object_id'); }
}
