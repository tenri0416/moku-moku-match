<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTrainingPointHistory;
use App\Models\VocabularyReview;
use App\Models\VocabularyWord;
use App\Services\Trainings\VocabularyAiTrainingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VocabularyTrainingController extends Controller
{
  public function __construct(
    private readonly VocabularyAiTrainingService $vocabularyAiTrainingService,
  ) {}

  public function index(Request $request): View
  {
    $this->abortUnlessVocabularyAllowed();

    $query = VocabularyWord::query()
      ->where('user_id', Auth::id())
      ->latest();

    if ($request->filled('keyword')) {
      $keyword = $request->keyword;

      $query->where(function ($q) use ($keyword) {
        $q->where('word', 'like', "%{$keyword}%")
          ->orWhere('meaning', 'like', "%{$keyword}%")
          ->orWhere('example_sentence', 'like', "%{$keyword}%");
      });
    }

    if ($request->filled('status')) {
      $query->where('review_status', $request->status);
    }

    $words = $query->paginate(20)->withQueryString();

    $totalWords = VocabularyWord::where('user_id', Auth::id())->count();

    $reviewTargetCount = VocabularyWord::where('user_id', Auth::id())
      ->where('is_review_target', true)
      ->count();

    $weakCount = VocabularyWord::where('user_id', Auth::id())
      ->where('review_status', VocabularyWord::STATUS_WEAK)
      ->count();

    $masteredCount = VocabularyWord::where('user_id', Auth::id())
      ->where('review_status', VocabularyWord::STATUS_MASTERED)
      ->count();

    return view('trainings.vocabulary.index', compact(
      'words',
      'totalWords',
      'reviewTargetCount',
      'weakCount',
      'masteredCount'
    ));
  }

  public function create(): View
  {
    $this->abortUnlessVocabularyAllowed();

    return view('trainings.vocabulary.create');
  }

  public function store(Request $request): RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();

    $validated = $request->validate([
      'word' => ['required', 'string', 'max:120'],
      'meaning' => ['required', 'string', 'max:3000'],
      'example_sentence' => ['required', 'string', 'max:3000'],
      'memo' => ['nullable', 'string', 'max:3000'],
      'source' => ['nullable', 'string', 'max:255'],
      'category' => ['nullable', 'string', 'max:100'],
      'importance' => ['required', 'integer', 'min:1', 'max:5'],
      'is_review_target' => ['nullable', 'boolean'],
    ], [
      'word.required' => '言葉を入力してください。',
      'meaning.required' => '意味を入力してください。',
      'example_sentence.required' => '例文を入力してください。',
    ]);

    DB::transaction(function () use ($validated) {
      foreach ($this->allowedVocabularyUsers() as $user) {
        VocabularyWord::create([
          'user_id' => $user->id,
          'word' => $validated['word'],
          'meaning' => $validated['meaning'],
          'example_sentence' => $validated['example_sentence'],
          'memo' => $validated['memo'] ?? null,
          'source' => $validated['source'] ?? null,
          'category' => $validated['category'] ?? null,
          'importance' => $validated['importance'],
          'is_review_target' => (bool) ($validated['is_review_target'] ?? true),
          'review_status' => VocabularyWord::STATUS_NOT_REVIEWED,
          'review_count' => 0,
          'correct_count' => 0,
          'last_reviewed_at' => null,
        ]);
      }
    });

    return redirect()
      ->route('trainings.vocabulary.index')
      ->with('success', 'ボキャブラリーを登録し、共有ユーザーにも反映しました。');
  }

  public function edit(VocabularyWord $word): View
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnVocabularyWord($word);

    return view('trainings.vocabulary.edit', compact('word'));
  }

  public function update(Request $request, VocabularyWord $word): RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnVocabularyWord($word);

    $original = [
      'word' => $word->word,
      'meaning' => $word->meaning,
      'example_sentence' => $word->example_sentence,
    ];

    $validated = $request->validate([
      'word' => ['required', 'string', 'max:120'],
      'meaning' => ['required', 'string', 'max:3000'],
      'example_sentence' => ['required', 'string', 'max:3000'],
      'memo' => ['nullable', 'string', 'max:3000'],
      'source' => ['nullable', 'string', 'max:255'],
      'category' => ['nullable', 'string', 'max:100'],
      'importance' => ['required', 'integer', 'min:1', 'max:5'],
      'is_review_target' => ['nullable', 'boolean'],
    ], [
      'word.required' => '言葉を入力してください。',
      'meaning.required' => '意味を入力してください。',
      'example_sentence.required' => '例文を入力してください。',
    ]);

    DB::transaction(function () use ($original, $validated, $word) {
      $targetUserIds = $this->allowedVocabularyUsers()->pluck('id');

      $updated = VocabularyWord::query()
        ->whereIn('user_id', $targetUserIds)
        ->where('word', $original['word'])
        ->where('meaning', $original['meaning'])
        ->where('example_sentence', $original['example_sentence'])
        ->update([
          'word' => $validated['word'],
          'meaning' => $validated['meaning'],
          'example_sentence' => $validated['example_sentence'],
          'memo' => $validated['memo'] ?? null,
          'source' => $validated['source'] ?? null,
          'category' => $validated['category'] ?? null,
          'importance' => $validated['importance'],
          'is_review_target' => (bool) ($validated['is_review_target'] ?? false),
          'updated_at' => now(),
        ]);

      if ($updated <= 0) {
        $word->update([
          'word' => $validated['word'],
          'meaning' => $validated['meaning'],
          'example_sentence' => $validated['example_sentence'],
          'memo' => $validated['memo'] ?? null,
          'source' => $validated['source'] ?? null,
          'category' => $validated['category'] ?? null,
          'importance' => $validated['importance'],
          'is_review_target' => (bool) ($validated['is_review_target'] ?? false),
        ]);
      }

      $this->createMissingSharedWordsFromSameContent($validated);
    });

    return redirect()
      ->route('trainings.vocabulary.index')
      ->with('success', 'ボキャブラリーを更新し、共有ユーザーにも反映しました。');
  }

  public function destroy(VocabularyWord $word): RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnVocabularyWord($word);

    $original = [
      'word' => $word->word,
      'meaning' => $word->meaning,
      'example_sentence' => $word->example_sentence,
    ];

    DB::transaction(function () use ($original, $word) {
      $targetUserIds = $this->allowedVocabularyUsers()->pluck('id');

      $deleted = VocabularyWord::query()
        ->whereIn('user_id', $targetUserIds)
        ->where('word', $original['word'])
        ->where('meaning', $original['meaning'])
        ->where('example_sentence', $original['example_sentence'])
        ->delete();

      if ($deleted <= 0) {
        $word->delete();
      }
    });

    return redirect()
      ->route('trainings.vocabulary.index')
      ->with('success', 'ボキャブラリーを削除し、共有ユーザーにも反映しました。');
  }

  public function review(Request $request): View|RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();

    $word = null;

    if ($request->filled('word_id')) {
      $word = VocabularyWord::query()
        ->where('id', $request->integer('word_id'))
        ->where('user_id', Auth::id())
        ->where('is_review_target', true)
        ->first();
    }

    $word ??= $this->selectReviewWord();

    if (! $word) {
      return redirect()
        ->route('trainings.vocabulary.create')
        ->with('error', '復習対象の言葉がありません。まずは言葉を登録してください。');
    }

    $questionType = $this->selectQuestionType($word);
    $questionBody = $this->makeQuestionBody($word, $questionType);

    return view('trainings.vocabulary.review', compact('word', 'questionType', 'questionBody'));
  }

  public function storeReview(Request $request): RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();
  
    $validated = $request->validate([
      'vocabulary_word_id' => ['required', 'integer', 'exists:vocabulary_words,id'],
      'question_type' => ['required', 'string', 'max:50'],
      'question_body' => ['required', 'string', 'max:3000'],
      'answer_body' => ['required', 'string', 'min:5', 'max:3000'],
    ], [
      'answer_body.required' => '回答を入力してください。',
      'answer_body.min' => '回答は5文字以上で入力してください。',
    ]);
  
    $word = VocabularyWord::where('id', $validated['vocabulary_word_id'])
      ->where('user_id', Auth::id())
      ->firstOrFail();
  
    return DB::transaction(function () use ($validated, $word) {
      $score = $this->vocabularyAiTrainingService->score(
        word: $word,
        questionType: $validated['question_type'],
        questionBody: $validated['question_body'],
        answerBody: $validated['answer_body']
      );
  
      // ボキャブラリートレーニングはポイント付与対象外
      $earnedPoints = 0;
  
      $review = VocabularyReview::create([
        'user_id' => Auth::id(),
        'vocabulary_word_id' => $word->id,
        'question_type' => $validated['question_type'],
        'question_body' => $validated['question_body'],
        'answer_body' => $validated['answer_body'],
        'total_score' => $score['total_score'],
        'meaning_score' => $score['meaning_score'],
        'explanation_score' => $score['explanation_score'],
        'usage_score' => $score['usage_score'],
        'retention_score' => $score['retention_score'],
        'good_point' => $score['good_point'],
        'improvement_point' => $score['improvement_point'],
        'correct_meaning' => $score['correct_meaning'],
        'next_task' => $score['next_task'],
        'earned_points' => $earnedPoints,
        'ai_provider' => $score['ai_provider'] ?? null,
        'ai_model' => $score['ai_model'] ?? null,
        'ai_status' => $score['ai_status'] ?? null,
        'ai_error_message' => $score['ai_error_message'] ?? null,
        'is_fallback' => $score['is_fallback'] ?? false,
        'ai_attempts' => $score['ai_attempts'] ?? 1,
        'reviewed_at' => now(),
      ]);
  
      $this->updateWordReviewStatus($word, (int) $score['total_score']);
  
      return redirect()
        ->route('trainings.vocabulary.reviews.show', $review)
        ->with('success', 'ボキャブラリー復習を保存しました。');
    });
  }

  public function showReview(VocabularyReview $review): View
  {
    $this->abortUnlessVocabularyAllowed();

    abort_unless($review->user_id === Auth::id(), 403);

    $review->load('vocabularyWord');

    return view('trainings.vocabulary.review-show', compact('review'));
  }

  private function selectReviewWord(): ?VocabularyWord
  {
    return VocabularyWord::query()
      ->where('user_id', Auth::id())
      ->where('is_review_target', true)
      ->orderByRaw(
        'CASE review_status WHEN ? THEN 0 WHEN ? THEN 1 WHEN ? THEN 2 ELSE 3 END',
        [
          VocabularyWord::STATUS_WEAK,
          VocabularyWord::STATUS_NOT_REVIEWED,
          VocabularyWord::STATUS_REVIEWING,
        ]
      )
      ->orderBy('last_reviewed_at')
      ->orderByDesc('importance')
      ->inRandomOrder()
      ->first();
  }

  private function selectQuestionType(VocabularyWord $word): string
  {
    $types = [
      '意味を答える問題',
      '例文を作る問題',
      '使い方を説明する問題',
    ];

    if ($word->review_count <= 0) {
      return '意味を答える問題';
    }

    return $types[array_rand($types)];
  }

  private function makeQuestionBody(VocabularyWord $word, string $questionType): string
  {
    return match ($questionType) {
      '例文を作る問題' => "「{$word->word}」を使って例文を作ってください。",
      '使い方を説明する問題' => "「{$word->word}」はどのような場面で使える言葉か説明してください。",
      default => "「{$word->word}」の意味を、自分の言葉で説明してください。",
    };
  }

  private function calculateEarnedPoints(int $totalScore): int
  {
    return match (true) {
      $totalScore >= 100 => 10,
      $totalScore >= 90 => 9,
      $totalScore >= 80 => 8,
      $totalScore >= 70 => 7,
      $totalScore >= 60 => 6,
      default => 1,
    };
  }

  private function updateWordReviewStatus(VocabularyWord $word, int $totalScore): void
  {
    $status = match (true) {
      $totalScore >= 90 => VocabularyWord::STATUS_MASTERED,
      $totalScore >= 80 => VocabularyWord::STATUS_UNDERSTOOD,
      $totalScore >= 60 => VocabularyWord::STATUS_REVIEWING,
      default => VocabularyWord::STATUS_WEAK,
    };

    $word->increment('review_count');

    if ($totalScore >= 80) {
      $word->increment('correct_count');
    }

    $word->update([
      'review_status' => $status,
      'last_reviewed_at' => now(),
    ]);
  }

  private function storePoint(VocabularyReview $review, int $points): void
  {
    UserTrainingPointHistory::create([
      'user_id' => Auth::id(),
      'training_type' => 'vocabulary',
      'training_id' => $review->id,
      'point_type' => 'training',
      'points' => $points,
      'earned_on' => now()->toDateString(),
      'note' => 'ボキャブラリー復習実施',
    ]);
  }

  private function allowedVocabularyEmails(): array
  {
    return collect()
      ->merge(config('services.reading_reflection.allowed_emails', []))
      ->merge(config('services.vocabulary.allowed_emails', []))
      ->merge(config('services.allowed_emails', []))
      ->map(fn($email) => mb_strtolower(trim((string) $email)))
      ->filter()
      ->unique()
      ->values()
      ->all();
  }

  private function isVocabularyAllowedUser(): bool
  {
    $user = Auth::user();

    if (! $user) {
      return false;
    }

    $email = mb_strtolower(trim((string) $user->email));

    return in_array($email, $this->allowedVocabularyEmails(), true);
  }

  private function abortUnlessVocabularyAllowed(): void
  {
    abort_unless($this->isVocabularyAllowedUser(), 403);
  }

  private function allowedVocabularyUsers()
  {
    return User::query()
      ->whereIn(
        DB::raw('LOWER(email)'),
        $this->allowedVocabularyEmails()
      )
      ->get();
  }

  private function abortUnlessOwnVocabularyWord(VocabularyWord $word): void
  {
    abort_unless((int) $word->user_id === (int) Auth::id(), 403);
  }

  private function createMissingSharedWordsFromSameContent(array $validated): void
  {
    $allowedUsers = $this->allowedVocabularyUsers();

    foreach ($allowedUsers as $user) {
      $exists = VocabularyWord::query()
        ->where('user_id', $user->id)
        ->where('word', $validated['word'])
        ->where('meaning', $validated['meaning'])
        ->where('example_sentence', $validated['example_sentence'])
        ->exists();

      if ($exists) {
        continue;
      }

      VocabularyWord::create([
        'user_id' => $user->id,
        'word' => $validated['word'],
        'meaning' => $validated['meaning'],
        'example_sentence' => $validated['example_sentence'],
        'memo' => $validated['memo'] ?? null,
        'source' => $validated['source'] ?? null,
        'category' => $validated['category'] ?? null,
        'importance' => $validated['importance'],
        'is_review_target' => (bool) ($validated['is_review_target'] ?? false),
        'review_status' => VocabularyWord::STATUS_NOT_REVIEWED,
        'review_count' => 0,
        'correct_count' => 0,
        'last_reviewed_at' => null,
      ]);
    }
  }
}
