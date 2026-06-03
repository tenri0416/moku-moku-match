<?php

namespace App\Services\Trainings\Ai;

class LocalTrainingScoringService
{
    public function score(string $text): array
    {
        $plainText = trim(strip_tags($text));
        $length = mb_strlen($plainText);

        $readabilityScore = $this->clampScore(
            10
            + (str_contains($plainText, '。') ? 5 : 0)
            + (str_contains($plainText, "\n") ? 5 : 0)
            + ($length >= 100 ? 5 : 0)
        );

        $specificityScore = $this->clampScore(
            8
            + (preg_match('/[0-9０-９]/u', $plainText) ? 5 : 0)
            + (preg_match('/なぜ|理由|ため|だから|ので/u', $plainText) ? 6 : 0)
            + ($length >= 150 ? 6 : 0)
        );

        $structureScore = $this->clampScore(
            8
            + (preg_match('/まず|次に|最後|結論|理由|改善/u', $plainText) ? 7 : 0)
            + (substr_count($plainText, "\n") >= 2 ? 5 : 0)
            + ($length >= 120 ? 5 : 0)
        );

        $expressionScore = $this->clampScore(
            10
            + ($length >= 80 ? 5 : 0)
            + ($length >= 200 ? 5 : 0)
            + (preg_match('/感じ|学び|気づき|改善|挑戦/u', $plainText) ? 5 : 0)
        );

        $totalScore = min(100, $readabilityScore + $specificityScore + $structureScore + $expressionScore);

        return [
            'total_score' => $totalScore,
            'readability_score' => $readabilityScore,
            'specificity_score' => $specificityScore,
            'structure_score' => $structureScore,
            'expression_score' => $expressionScore,
            'good_point' => '簡易採点で評価しました。内容を記録できている点は良いです。',
            'improvement_point' => '理由、具体例、学びを加えると、より伝わりやすくなります。',
            'next_task' => '次回は「出来事、理由、学び」を入れて書いてみましょう。',
        ];
    }

    private function clampScore(int $score): int
    {
        return max(0, min(25, $score));
    }
}
