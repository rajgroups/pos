<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypes = [
            ['name' => 'Aadhaar', 'slug' => 'aadhaar', 'for_type' => 'driver', 'has_expiry' => false, 'is_required' => true, 'is_active' => true],
            ['name' => 'PAN', 'slug' => 'pan', 'for_type' => 'driver', 'has_expiry' => false, 'is_required' => true, 'is_active' => true],
            ['name' => 'Driving License', 'slug' => 'driving-license', 'for_type' => 'driver', 'has_expiry' => true, 'is_required' => true, 'is_active' => true],
            ['name' => 'Police Verification', 'slug' => 'police-verification', 'for_type' => 'driver', 'has_expiry' => true, 'is_required' => false, 'is_active' => true],
            ['name' => 'Medical Certificate', 'slug' => 'medical-certificate', 'for_type' => 'driver', 'has_expiry' => true, 'is_required' => false, 'is_active' => true],
            ['name' => 'RC', 'slug' => 'rc', 'for_type' => 'vehicle', 'has_expiry' => true, 'is_required' => true, 'is_active' => true],
            ['name' => 'Insurance', 'slug' => 'insurance', 'for_type' => 'vehicle', 'has_expiry' => true, 'is_required' => true, 'is_active' => true],
            ['name' => 'Permit', 'slug' => 'permit', 'for_type' => 'vehicle', 'has_expiry' => true, 'is_required' => true, 'is_active' => true],
            ['name' => 'Pollution Certificate', 'slug' => 'pollution', 'for_type' => 'vehicle', 'has_expiry' => true, 'is_required' => false, 'is_active' => true],
            ['name' => 'Fitness Certificate', 'slug' => 'fitness', 'for_type' => 'vehicle', 'has_expiry' => true, 'is_required' => true, 'is_active' => true],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::query()->updateOrCreate(
                ['slug' => $documentType['slug']],
                $documentType
            );
        }
    }
}
