<?php

namespace App\Providers;

use App\Interfaces\BrandInterface;
use App\Interfaces\CategoryInterface;
use App\Interfaces\StoreInterface;
use App\Interfaces\UnitInterface;
use App\Interfaces\VariantAttributeInterface;
use App\Interfaces\WarehouseInterface;
use App\Interfaces\WarrantyInterface;
use App\Repositories\BrandRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\StoreRepository;
use App\Repositories\UnitRepository;
use App\Repositories\VariantAttributeRepository;
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
        $this->app->bind(BrandInterface::class, BrandRepository::class);
        $this->app->bind(UnitInterface::class, UnitRepository::class);
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
