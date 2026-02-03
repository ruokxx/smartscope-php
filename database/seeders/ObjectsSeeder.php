<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Obj;

class ObjectsSeeder extends Seeder {
    public function run() {
        Obj::firstOrCreate(['name'=>'M31','catalog'=>'Messier 31','ra'=>'00h42m44.3s','dec'=>'+41°16′9″','type'=>'Galaxy','description'=>'Andromeda Galaxy']);
        Obj::firstOrCreate(['name'=>'M42','catalog'=>'Messier 42','ra'=>'05h35m17.3s','dec'=>'−05°23′28″','type'=>'Nebula','description'=>'Orion Nebula']);
    }
}
