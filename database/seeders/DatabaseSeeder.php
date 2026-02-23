<?php

namespace Database\Seeders;

use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(SubCategorySeeder::class);
        $this->call(ChildCategorySeeder::class);
        $this->call(BrandSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductVariantSeeder::class);
        $this->call(ProductVariantItemSeeder::class);
        $this->call(PermissionSeeder::class);
    }
}
