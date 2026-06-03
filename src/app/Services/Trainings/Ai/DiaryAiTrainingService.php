<?php

namespace App\Services\Trainings\Ai;

class DiaryAiTrainingService extends BaseAiTrainingService
{
    public function score(string $diaryBody, int|string $difficulty = 0): array
    {
        $prompt = <<<PROMPT
あなたは日本語検定の観点を持つ文章トレーニングの先生です。
以下の日記を採点してください。

目的：
- 正しい日本語で書けているかを見る
- 内容の具体性も見る
- ただし、ユーザーの継続意欲を下げない表現にする

採点基準：
- 総合点：100点満点
- 読みやすさ：25点満点
  - 句読点、文の長さ、自然な流れ
- 具体性：25点満点
  - いつ、どこで、何を、なぜ、どう感じたか
- 構成：25点満点
  - 出来事、感情、理由、学びの流れ
- 表現力：25点満点
  - 文法、助詞、語彙、主語述語、敬体/常体の統一

日本語検定寄りの確認項目：
- 助詞の使い方が自然か
- 文法が正しいか
- 主語と述語が対応しているか
- 語彙の使い方が正しいか
- 句読点が適切か
- 表記ゆれが少ないか

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

日記：
{$diaryBody}
PROMPT;

        return $this->requestScore(
            prompt: $prompt,
            fallbackText: $diaryBody,
            actionName: '日記トレーニング採点',
        );
    }
}
