<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_title' => $this->faker->jobTitle,
            'email' => $this->faker->unique()->safeEmail,
            'first_name_last_name' => $this->faker->name,
            'registered_since' => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d H:i:s'),
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
