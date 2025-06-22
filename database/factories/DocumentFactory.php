<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => $this->faker->unique()->numerify('student-####'),
            'filename' => $this->faker->word() . '.pdf',
            's3_url' => 'student-documents/' . $this->faker->uuid() . '.pdf',
        ];
    }
}
