<?php

namespace Database\Factories;

use App\Models\Fonction;
use Illuminate\Database\Eloquent\Factories\Factory;

class FonctionFactory extends Factory
{
    protected $model = Fonction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Admin', 'Coach', 'Manager', 'Apprenant', 'CM'
            ]),
        ];
    }
}

