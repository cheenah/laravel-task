<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TasksSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->info('Нет пользователей, сначала создай пользователей!');
            return;
        }

        foreach (range(1, 100) as $i) {
            Task::create([
                'author_id' => $users->random()->id,
                'title' => "Заявка #$i",
                'description' => rand(0,1) ? "Описание заявки #$i" : null,
                'status' => ['new', 'in_progress', 'done', 'cancelled'][array_rand(['new','in_progress','done','cancelled'])]
            ]);
        }
    }
}
