<?php

namespace App\Providers;

use App\Interfaces\BrandInterface;
use App\Interfaces\CategoryInterface;
use App\Interfaces\DocumentTypeInterface;
use App\Interfaces\DriverDocumentInterface;
use App\Interfaces\DriverInterface;
use App\Interfaces\DriverVehicleAssignmentInterface;
use App\Interfaces\StoreInterface;
use App\Interfaces\UnitInterface;
use App\Interfaces\VariantAttributeInterface;
use App\Interfaces\VehicleCategoryPricingInterface;
use App\Interfaces\VehicleDocumentInterface;
use App\Interfaces\VehicleInterface;
use App\Interfaces\WarehouseInterface;
use App\Interfaces\WarrantyInterface;
use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\DocumentTypeRepository;
use App\Repositories\DriverDocumentRepository;
use App\Repositories\DriverRepository;
use App\Repositories\DriverVehicleAssignmentRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UnitRepository;
use App\Repositories\VariantAttributeRepository;
use App\Repositories\VehicleCategoryPricingRepository;
use App\Repositories\VehicleDocumentRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\WarrantyRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        $this->app->bind(CategoryInterface::class, CategoryRepository::class);
        $this->app->bind(DriverInterface::class, DriverRepository::class);
        $this->app->bind(BrandInterface::class, BrandRepository::class);
        $this->app->bind(DocumentTypeInterface::class, DocumentTypeRepository::class);
        $this->app->bind(DriverDocumentInterface::class, DriverDocumentRepository::class);
        $this->app->bind(VehicleDocumentInterface::class, VehicleDocumentRepository::class);
        $this->app->bind(DriverVehicleAssignmentInterface::class, DriverVehicleAssignmentRepository::class);
        $this->app->bind(VehicleCategoryPricingInterface::class, VehicleCategoryPricingRepository::class);
        $this->app->bind(UnitInterface::class, UnitRepository::class);
        $this->app->bind(VehicleInterface::class, VehicleRepository::class);
        $this->app->bind(VariantAttributeInterface::class, VariantAttributeRepository::class);
        $this->app->bind(WarrantyInterface::class,WarrantyRepository::class);
        $this->app->bind(WarehouseInterface::class,WarehouseRepository::class);
        $this->app->bind(StoreInterface::class,StoreRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
