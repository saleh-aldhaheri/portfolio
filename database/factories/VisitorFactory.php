<?php

namespace Database\Factories;

use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'os' => 'mac',
            'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Opera', 'Safari']),
            'company' => $this->faker->company(),
            'occupation' => $this->faker->jobTitle(),
        ];
    }
}
