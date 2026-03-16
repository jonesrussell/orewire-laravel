<?php

namespace Database\Factories;

use App\Models\Commodity;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Commodity> */
class CommodityFactory extends Factory
{
    protected $model = Commodity::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $name = fake()->unique()->word().' Metal';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'symbol' => strtoupper(substr($name, 0, 2)),
        ];
    }
}
