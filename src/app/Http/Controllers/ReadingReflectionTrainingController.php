<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReadingReflectionTrainingRequest;
use App\Models\User;
use App\Models\UserReadingReflectionTraining;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ReadingReflectionTrainingController extends Controller
{
    /**
     * 読書振り返り履歴画面を表示する。
     */
    public function index(): View
    {
        $user = Auth::user();

        Log::info("こおれはテストログです。");
        abort_unless($user instanceof User && $this->canUseReadingReflection($user), 404);

        $todayReflection = UserReadingReflectionTraining::query()
            ->where('user_id', $user->id)
            ->whereDate('read_on', now()->toDateString())
            ->first();

        $reflections = UserReadingReflectionTraining::query()
            ->where('user_id', $user->id)
            ->latest('read_on')
            ->latest('id')
            ->paginate(20);

        return view('reading-reflection-trainings.index', compact(
            'todayReflection',
            'reflections'
        ));
    }

    /**
     * 読書振り返りを保存する。
     *
     * 同じ日の記録がある場合は、新規作成ではなく更新する。
     */
    public function store(StoreReadingReflectionTrainingRequest $request): RedirectResponse
    {
        $user = Auth::user();

        abort_unless($user instanceof User && $this->canUseReadingReflection($user), 404);

        $readOn = $request->filled('read_on')
            ? Carbon::parse($request->input('read_on'))->toDateString()
            : now()->toDateString();

        UserReadingReflectionTraining::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'read_on' => $readOn,
            ],
            [
                'book_title' => $request->input('book_title'),
                'read_minutes' => (int) $request->input('read_minutes', 10),
                'mood' => $request->input('mood'),
                'reflection_text' => $request->input('reflection_text'),
            ]
        );

        return back()->with('success', '読書の振り返りを保存しました。');
    }

    /**
     * 読書振り返りトレーニングを使えるユーザーか判定する。
     */
    private function canUseReadingReflection(User $user): bool
    {
        $allowedEmails = collect(config('services.reading_reflection.allowed_emails', []))
            ->map(fn(string $email): string => strtolower(trim($email)))
            ->filter();

            
        return $allowedEmails->contains(strtolower($user->email));
    }
}
