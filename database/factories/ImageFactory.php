<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'url' => 'https://www.freedigitalphotos.net/images/img/homepage/394230.jpg',
            'gallery_id' => $this->faker->numberBetween(1, 10),
            'order_index' => $this->faker->randomDigit,
        ];
    }
}
