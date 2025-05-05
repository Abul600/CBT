<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        // Get only active paper setters and moderators
        $paperSetters = User::role('paper_setter')
            ->where('is_active', true)
            ->pluck('id');
            
        $moderators = User::role('moderator')
            ->where('is_active', true)
            ->pluck('id');

        Question::factory()->count(50)->create([
            'paper_setter_id' => fn() => $paperSetters->random(),
            'moderator_id' => fn() => in_array($status = fake()->randomElement([
                Question::STATUS_SENT,
                Question::STATUS_APPROVED,
                Question::STATUS_REJECTED
            ]), [Question::STATUS_DRAFT]) ? null : $moderators->random(),
            'question_text' => fake()->sentence(12) . '?',
            'options' => function(array $attributes) {
                return in_array($attributes['type'], [
                    Question::TYPE_MCQ_SINGLE, 
                    Question::TYPE_MCQ_MULTIPLE
                ]) ? [
                    fake()->sentence(3),
                    fake()->sentence(3),
                    fake()->sentence(3),
                    fake()->sentence(3)
                ] : null;
            },
            'correct_answer' => function(array $attributes) {
                return $attributes['type'] === Question::TYPE_DESCRIPTIVE 
                    ? null 
                    : rand(0, 3);
            },
            'type' => fake()->randomElement([
                Question::TYPE_MCQ_SINGLE,
                Question::TYPE_MCQ_MULTIPLE,
                Question::TYPE_DESCRIPTIVE
            ]),
            'status' => fake()->randomElement([
                Question::STATUS_DRAFT,
                Question::STATUS_SENT,
                Question::STATUS_APPROVED,
                Question::STATUS_REJECTED
            ]),
            'sent_at' => fn(array $attributes) => $attributes['status'] !== Question::STATUS_DRAFT 
                ? now()->subDays(rand(1, 30)) 
                : null,
        ]);

        $this->command->info('50 valid questions created with proper relationships!');
    }
}