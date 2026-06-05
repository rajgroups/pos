<?php

namespace App\Models;

/**
 * Backward-compatible alias for the vehicle_categories model.
 *
 * Existing API and seeders still reference VehicleType, so we keep this class
 * as the public entry point while VehicleCategory holds the shared relations.
 */
class VehicleType extends VehicleCategory
{
}
