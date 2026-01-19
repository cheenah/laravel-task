<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = Task::all();
        $users = User::all();

        if ($tasks->isEmpty() || $users->isEmpty()) {
            return;
        }

        foreach ($tasks as $task) {
            for ($i = 0; $i < rand(2, 5); $i++) {
                Comment::create([
                    'task_id' => $task->id,
                    'author_id' => $users->random()->id,
                    'content' => 'Это тестовый комментарий номер ' . ($i + 1) . ' для задачи: ' . $task->title,
                    'created_at' => now()->subMinutes(rand(1, 1000)),
                ]);
            }
        }
    }
}
