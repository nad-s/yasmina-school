<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json(Classroom::with('teacher', 'students')->get());
        }

        if ($user->role === 'teacher') {
            return response()->json(
                Classroom::with('teacher', 'students')
                    ->where('teacher_id', $user->id)
                    ->get()
            );
        }

        return response()->json(
            Classroom::with('teacher')
                ->whereHas('students', fn($q) => $q->where('user_id', $user->id))
                ->get()
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $classroom = Classroom::create($validated);
        return response()->json($classroom->load('teacher'), 201);
    }

    public function show(Request $request, Classroom $classroom)
    {
        $user = $request->user();

        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($classroom->load('teacher', 'students'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $user = $request->user();

        if ($user->role === 'student') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'teacher' && $classroom->teacher_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        if ($user->role === 'teacher') {
            unset($validated['teacher_id']);
        }

        $classroom->update($validated);
        return response()->json($classroom->load('teacher'));
    }

    public function destroy(Request $request, Classroom $classroom)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $classroom->delete();
        return response()->json(['message' => 'Classroom deleted']);
    }
}