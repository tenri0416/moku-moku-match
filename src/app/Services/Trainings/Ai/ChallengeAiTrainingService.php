<?php

namespace App\Services\Trainings\Ai;

class ChallengeAiTrainingService extends BaseAiTrainingService
{
    public function score(array $data, int|string $difficulty = 0): array
    {
        $fallbackText = implode("\n", [
            $data['challenged_thing'] ?? '',
            $data['completed_thing'] ?? '',
            $data['difficult_thing'] ?? '',
            $data['next_improvement'] ?? '',
        ]);

        $prompt = <<<PROMPT
あなたは行動改善トレーニングの先生です。
以下の「今日のチャレンジ」を採点してください。

採点基準：
- 総合点：100点満点
- 読みやすさ：25点満点
- 具体性：25点満点
- 構成：25点満点
- 表現力：25点満点

重視する点：
- チャレンジ内容が具体的か
- できたことが明確か
- 難しかったことを言語化できているか
- 次の改善行動が具体的か

難易度：
{$difficulty}

{$this->shortJsonRule()}

必ず次のJSON形式だけで返してください。

{
  "total_score": 78,
  "readability_score": 20,
  "specificity_score": 18,
  "structure_score": 17,
  "expression_score": 23,
  "good_point": "良い点",
  "improvement_point": "改善点",
  "next_task": "次回の課題"
}

今日チャレンジしたこと：
{$data['challenged_thing']}

できたこと：
{$data['completed_thing']}

難しかったこと：
{$data['difficult_thing']}

次に改善したいこと：
{$data['next_improvement']}
PROMPT;

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $fallbackText,
            actionName: '今日のチャレンジ採点',
        );
    }
}
