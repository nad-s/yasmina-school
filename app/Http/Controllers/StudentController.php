<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json(Student::with('user', 'classroom')->paginate(10));
        }

        if ($user->role === 'teacher') {
            return response()->json(
                Student::with('user', 'classroom')
                    ->whereHas('classroom', fn($q) => $q->where('teacher_id', $user->id))
                    ->paginate(10)
            );
        }

        return response()->json(
            Student::with('user', 'classroom')
                ->where('user_id', $user->id)
                ->get()
        );
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date_of_birth' => 'nullable|date',
            'assigned_class_id' => 'nullable|exists:classrooms,id',
            'grade' => 'nullable|string',
        ]);

        $student = Student::create($validated);
        return response()->json($student->load('user', 'classroom'), 201);
    }

    public function show(Request $request, Student $student)
    {
        $user = $request->user();

        if ($user->role === 'student' && $student->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'teacher') {
            $classroom = $student->classroom;
            if (!$classroom || $classroom->teacher_id !== $user->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        return response()->json($student->load('user', 'classroom'));
    }

    public function update(Request $request, Student $student)
    {
        $user = $request->user();

        if ($user->role === 'student' && $student->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->role === 'teacher') {
            $classroom = $student->classroom;
            if (!$classroom || $classroom->teacher_id !== $user->id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $validated = $request->validate([
            'date_of_birth' => 'nullable|date',
            'assigned_class_id' => 'nullable|exists:classrooms,id',
            'grade' => 'nullable|string',
        ]);

        if ($user->role === 'student') {
            unset($validated['assigned_class_id'], $validated['grade']);
        }

        $student->update($validated);
        return response()->json($student->load('user', 'classroom'));
    }

    public function destroy(Request $request, Student $student)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $student->delete();
        return response()->json(['message' => 'Student deleted']);
    }
}