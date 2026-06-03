<?php

namespace App\Services\Trainings\Ai;

use RuntimeException;

class TrainingAiScoringService
{
    public function __construct(
        private readonly DiaryAiTrainingService $diaryAiTrainingService,
        private readonly ChallengeAiTrainingService $challengeAiTrainingService,
        private readonly SummaryAiTrainingService $summaryAiTrainingService,
        private readonly VerbalizationAiTrainingService $verbalizationAiTrainingService,
        private readonly AbstractionAiTrainingService $abstractionAiTrainingService,
        private readonly ConcretizationAiTrainingService $concretizationAiTrainingService,
    ) {
    }

    public function scoreDiary(string $diaryBody, int|string $difficulty = 0): array
    {
        return $this->diaryAiTrainingService->score($diaryBody, $difficulty);
    }

    public function scoreChallenge(array $data, int|string $difficulty = 0): array
    {
        return $this->challengeAiTrainingService->score($data, $difficulty);
    }

    public function generateAiTrainingQuestion(string $type, int|string $difficulty = 0): array
    {
        return $this->questionService($type)->generateQuestion($difficulty);
    }

    public function scoreAiTraining(
        string $type,
        string $questionTitle,
        string $questionBody,
        string $answerBody,
        int|string $difficulty = 0
    ): array {
        return $this->questionService($type)->scoreAnswer(
            questionTitle: $questionTitle,
            questionBody: $questionBody,
            answerBody: $answerBody,
            difficulty: $difficulty,
        );
    }

    private function questionService(string $type): BaseQuestionAiTrainingService
    {
        return match ($type) {
            'summary' => $this->summaryAiTrainingService,
            'verbalization' => $this->verbalizationAiTrainingService,
            'abstraction' => $this->abstractionAiTrainingService,
            'concretization' => $this->concretizationAiTrainingService,
            default => throw new RuntimeException('不正なトレーニング種別です。'),
        };
    }
}
