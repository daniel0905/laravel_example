<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['image', 'video']),
            'label' => $this->faker->text(255),
            'file' => $this->faker->filePath(),
            'url' => $this->faker->lexify('??????/????-???'),
        ];
    }
}
