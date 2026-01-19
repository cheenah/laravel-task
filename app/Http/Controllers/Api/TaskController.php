<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Comment;
use Laravel\Passport\Token;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with('user:id,name');

        if (Auth::user()->hasRole('user')) {
            $query->where('author_id', Auth::id());
        }

        $query->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->orderBy('created_at', 'desc');

        return response()->json($query->paginate($request->per_page ?? 15));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:new,in_progress,done,cancelled'
        ]);

        $data['author_id'] = $user->id;
        $data['status'] = $data['status'] ?? 'new';

        $task = Task::create($data);

        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::with('user:id,name', 'comments')->findOrFail($id);

        if (Auth::user()->hasRole('user') && $task->author_id !== Auth::id()) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }

        return response()->json($task);
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if (
            (Auth::user()->hasRole('user') && $task->author_id !== Auth::id() && $task->status !== 'new')
            || Auth::user()->hasRole('manager')
        ) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|string'
        ]);

        $task->update($data);

        return response()->json($task);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if (Auth::user()->hasRole('user')) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }

        $data = $request->validate([
            'status' => 'required|string'
        ]);

        $task->update(['status' => $data['status']]);

        return response()->json($task);
    }

    public function addComment(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::guard('api')->user();

        if (Auth::user()->hasRole('user') && $task->author_id !== Auth::id()) {
            return response()->json(['message' => 'Доступ запрещён'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $task->comments()->create([
            'author_id' => $user->id,
            'content' => $data['content']
        ]);

        return response()->json($comment, 201);
    }
}
