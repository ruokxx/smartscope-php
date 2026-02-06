<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scope;

class ScopesExtraSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'Seestar S50',
            'Seestar S30',
            'Dwarf 2',
            'Dwarf 3',
            'Dwarf Mini'
        ];
        foreach ($names as $n) {
            Scope::firstOrCreate(['name' => $n]);
        }
    }
}
