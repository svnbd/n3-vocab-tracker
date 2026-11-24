<?php

namespace App\Http\Controllers;

use App\Models\VocabProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function index()
    {
        $progress = VocabProgress::where('user_id', Auth::id())
            ->orderBy('day_number')
            ->get();

        $completedDayNumbers = $progress->where('is_completed', true)
            ->pluck('day_number')
            ->map(fn ($dayNumber) => (int) $dayNumber)
            ->values();
        $completedDays = $completedDayNumbers->count();
        $percentage = round(($completedDays / 60) * 100, 1);

        return view('dashboard', compact('progress', 'completedDayNumbers', 'completedDays', 'percentage'));
    }

    public function saveMockTest(Request $request)
    {
        $validated = $request->validate([
            'day_number' => 'required|integer|min:1|max:60',
            'score' => 'required|integer|min:0|max:10',
        ]);

        $passed = $validated['score'] >= 7;

        $progress = VocabProgress::firstOrNew(
            [
                'user_id' => Auth::id(),
                'day_number' => $validated['day_number'],
            ]
        );

        $progress->score = $validated['score'];
        $progress->passed = $passed;
        if ($passed) {
            $progress->is_completed = true;
            $progress->completed_at = now();
        } elseif (!$progress->is_completed) {
            $progress->completed_at = null;
        }
        $progress->save();

        $completedDayNumbers = $this->completedDayNumbers();
        $completedDays = count($completedDayNumbers);

        $percentage = round(($completedDays / 60) * 100, 1);

        return response()->json([
            'status' => 'success',
            'passed' => $passed,
            'completed_days' => $completedDays,
            'completed_day_numbers' => $completedDayNumbers,
            'percentage' => $percentage,
            'progress' => $progress,
        ]);
    }

    public function setDayCompletion(Request $request)
    {
        $validated = $request->validate([
            'day_number' => ['required', 'integer', 'min:1', 'max:60'],
            'completed' => ['required', 'boolean'],
        ]);

        $progress = VocabProgress::firstOrNew(
            [
                'user_id' => Auth::id(),
                'day_number' => $validated['day_number'],
            ],
            [
                'score' => 0,
                'passed' => false,
            ]
        );

        $progress->is_completed = $validated['completed'];
        $progress->completed_at = $validated['completed']
            ? ($progress->completed_at ?? now())
            : null;
        $progress->save();

        $completedDayNumbers = $this->completedDayNumbers();
        $completedDays = count($completedDayNumbers);

        return response()->json([
            'status' => 'success',
            'completed_days' => $completedDays,
            'completed_day_numbers' => $completedDayNumbers,
            'percentage' => round(($completedDays / 60) * 100, 1),
        ]);
    }

    public function resetProgress()
    {
        VocabProgress::where('user_id', Auth::id())->delete();

        return response()->json([
            'status' => 'success',
            'completed_days' => 0,
            'completed_day_numbers' => [],
            'percentage' => 0,
        ]);
    }

    private function completedDayNumbers(): array
    {
        return VocabProgress::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->orderBy('day_number')
            ->pluck('day_number')
            ->map(fn ($dayNumber) => (int) $dayNumber)
            ->all();
    }
}