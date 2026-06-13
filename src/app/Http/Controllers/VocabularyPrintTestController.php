<?php

namespace App\Http\Controllers;

use App\Models\VocabularyPrintTest;
use App\Models\VocabularyWord;
use App\Services\Trainings\VocabularyPrintTestBuilderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class VocabularyPrintTestController extends Controller
{
  public function __construct(
    private readonly VocabularyPrintTestBuilderService $builderService,
  ) {}

  public function index(): View
  {
    $this->abortUnlessVocabularyAllowed();

    $userId = Auth::id();

    $categories = VocabularyWord::query()
      ->where('user_id', $userId)
      ->whereNotNull('category')
      ->where('category', '<>', '')
      ->select('category')
      ->distinct()
      ->orderBy('category')
      ->pluck('category');

    $totalWords = VocabularyWord::query()
      ->where('user_id', $userId)
      ->count();

    $printTests = VocabularyPrintTest::query()
      ->where('user_id', $userId)
      ->latest()
      ->limit(10)
      ->get();

    return view('trainings.vocabulary.print.index', [
      'categories' => $categories,
      'totalWords' => $totalWords,
      'printTests' => $printTests,
      'questionTypes' => VocabularyPrintTestBuilderService::ALL_TYPES,
    ]);
  }

  public function store(Request $request): RedirectResponse
  {
    $this->abortUnlessVocabularyAllowed();

    $validated = $request->validate([
      'question_count' => ['required', 'integer', 'in:5,10,20,30'],
      'target_filter' => ['required', 'string', 'in:review_target,weak,not_reviewed,all'],
      'category' => ['nullable', 'string', 'max:100'],
      'importance' => ['nullable', 'integer', 'min:1', 'max:5'],
      'question_types' => ['required', 'array', 'min:1'],
      'question_types.*' => ['required', 'string', 'in:' . implode(',', VocabularyPrintTestBuilderService::ALL_TYPES)],
      'time_limit_minutes' => ['required', 'integer', 'min:1', 'max:180'],
    ], [
      'question_types.required' => '問題形式を1つ以上選択してください。',
    ]);

    try {
      $printTest = $this->builderService->create(Auth::user(), $validated);
    } catch (RuntimeException $e) {
      return back()
        ->withInput()
        ->with('error', $e->getMessage());
    } catch (\Throwable $e) {
      report($e);

      return back()
        ->withInput()
        ->with('error', '一部のAI問題生成に失敗したため、テストを作成できませんでした。時間をおいて再度お試しください。');
    }

    return redirect()
      ->route('trainings.vocabulary.print.show', $printTest)
      ->with('success', '印刷テストを作成しました。');
  }

  public function show(VocabularyPrintTest $printTest): View
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnPrintTest($printTest);

    $printTest->load('questions.vocabularyWord');

    return view('trainings.vocabulary.print.show', [
      'printTest' => $printTest,
    ]);
  }

  public function downloadQuestions(VocabularyPrintTest $printTest): Response
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnPrintTest($printTest);

    $printTest->load('questions.vocabularyWord');

    $pdf = Pdf::loadView('trainings.vocabulary.print.pdf.questions', [
      'printTest' => $printTest,
    ])->setPaper('a4', 'portrait');

    return $pdf->download($this->pdfFileName($printTest, '問題用紙'));
  }

  public function downloadAnswers(VocabularyPrintTest $printTest): Response
  {
    $this->abortUnlessVocabularyAllowed();
    $this->abortUnlessOwnPrintTest($printTest);

    $printTest->load('questions.vocabularyWord');

    $pdf = Pdf::loadView('trainings.vocabulary.print.pdf.answers', [
      'printTest' => $printTest,
    ])->setPaper('a4', 'portrait');

    return $pdf->download($this->pdfFileName($printTest, '模範解答'));
  }

  private function abortUnlessOwnPrintTest(VocabularyPrintTest $printTest): void
  {
    abort_unless((int) $printTest->user_id === (int) Auth::id(), 403);
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

  private function pdfFileName(VocabularyPrintTest $printTest, string $type): string
  {
    $date = optional($printTest->created_at)->format('Ymd') ?? now()->format('Ymd');

    return "ボキャブラリートレーニング_{$type}_{$date}_{$printTest->id}.pdf";
  }
}
