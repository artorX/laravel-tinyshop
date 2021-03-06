<?php

use App\Enums\EProductStatuses;
use App\Models\Brand;
use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    $productName = $faker->company . ' ' . $faker->word;
    $catsIds = \App\Models\Category::pluck('id')->toArray();
    $brandsIds = Brand::pluck('id')->toArray();

    return [
        'code' => Str::random(16),
        'name' => $productName,
        'description' => $faker->text,
        'status' => random_int(EProductStatuses::DRAFT, EProductStatuses::ACTIVE),
        'brand_id' => $faker->randomElement($brandsIds),
        'old_price' => $faker->randomFloat(2, 1, 9999),
        'price' => $faker->randomFloat(2, 1, 9999),
        'rating' => $faker->randomFloat(2, 0, 9.99),
        'meta_title' => $productName . ' - title',
        'meta_description' => $productName . ' - description',
        'meta_keywords' => $productName . ' - keywords',
    ];
});
