<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Scope;
use App\Models\Obj;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

class DemoUserSeeder extends Seeder {
    public function run() {
        // Use email as identifier (safer for default Laravel)
        $u = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'password' => Hash::make('password'),
            ]
        );

        $scope = Scope::where('name','Seestar')->first();
        $obj = Obj::where('name','M31')->first();

        // ensure storage folder and placeholder
        Storage::disk('public')->makeDirectory('uploads');
        $sample = storage_path('app/public/uploads/sample.jpg');
        if (!file_exists($sample)) file_put_contents($sample, ''); // placeholder

        Image::firstOrCreate(
            [
                'user_id' => $u->id,
                'object_id' => $obj->id,
                'scope_id' => $scope->id,
                'filename' => 'sample.jpg',
                'path' => 'public/uploads/sample.jpg',
            ],
            [
                'exposure_total_seconds' => 600,
                'number_of_subs' => 12,
                'processing_software' => 'PixInsight',
                'approved' => true
            ]
        );
    }
}
