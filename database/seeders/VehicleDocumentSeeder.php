<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documentTypeIds = DocumentType::query()
            ->where('for_type', 'vehicle')
            ->pluck('id', 'slug');

        $vehicles = Vehicle::query()->get([
            'id',
            'vehicle_number',
            'rc_number',
            'rc_expiry',
            'insurance_number',
            'insurance_expiry',
            'permit_number',
            'permit_expiry',
            'fitness_certificate_number',
            'fitness_expiry',
        ]);

        if ($documentTypeIds->isEmpty() || $vehicles->isEmpty()) {
            return;
        }

        $now = now();

        foreach ($vehicles as $index => $vehicle) {
            $documents = [
                [
                    'slug' => 'rc',
                    'document_number' => $vehicle->rc_number,
                    'expiry_date' => $vehicle->rc_expiry?->toDateString(),
                    'file_path' => "vehicles/documents/{$vehicle->id}/rc.jpg",
                    'status' => 'approved',
                ],
                [
                    'slug' => 'insurance',
                    'document_number' => $vehicle->insurance_number,
                    'expiry_date' => $vehicle->insurance_expiry?->toDateString(),
                    'file_path' => "vehicles/documents/{$vehicle->id}/insurance.jpg",
                    'status' => $index % 6 === 0 ? 'pending' : 'approved',
                ],
                [
                    'slug' => 'permit',
                    'document_number' => $vehicle->permit_number,
                    'expiry_date' => $vehicle->permit_expiry?->toDateString(),
                    'file_path' => "vehicles/documents/{$vehicle->id}/permit.jpg",
                    'status' => $index % 9 === 0 ? 'rejected' : 'approved',
                ],
                [
                    'slug' => 'pollution',
                    'document_number' => 'PUC' . str_pad((string) ($vehicle->id * 17), 6, '0', STR_PAD_LEFT),
                    'expiry_date' => Carbon::parse($vehicle->insurance_expiry ?? $now)->subMonths(2)->toDateString(),
                    'file_path' => "vehicles/documents/{$vehicle->id}/pollution.jpg",
                    'status' => $index % 7 === 0 ? 'pending' : 'approved',
                ],
                [
                    'slug' => 'fitness',
                    'document_number' => $vehicle->fitness_certificate_number,
                    'expiry_date' => $vehicle->fitness_expiry?->toDateString(),
                    'file_path' => "vehicles/documents/{$vehicle->id}/fitness.jpg",
                    'status' => 'approved',
                ],
            ];

            foreach ($documents as $document) {
                $documentTypeId = $documentTypeIds[$document['slug']] ?? null;

                if (!$documentTypeId) {
                    continue;
                }

                DB::table('vehicle_documents')->updateOrInsert(
                    [
                        'vehicle_id' => $vehicle->id,
                        'document_type_id' => $documentTypeId,
                    ],
                    [
                        'document_number' => $document['document_number'],
                        'file_path' => $document['file_path'],
                        'expiry_date' => $document['expiry_date'],
                        'status' => $document['status'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
