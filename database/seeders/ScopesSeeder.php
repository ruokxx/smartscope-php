<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Scope;

class ScopesSeeder extends Seeder {
    public function run() {
        Scope::firstOrCreate(['name'=>'Seestar']);
        Scope::firstOrCreate(['name'=>'Dwarf']);
    }
}
