<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    @php
        $pdfText = function (?string $text, int $limit = 42): string {
            $text = trim((string) $text);

            if ($text === '') {
                return '';
            }

            $lines = preg_split('/\R/u', $text) ?: [];
            $wrapped = [];

            foreach ($lines as $line) {
                $line = trim($line);

                if ($line === '') {
                    $wrapped[] = '';
                    continue;
                }

                foreach (mb_str_split($line, $limit, 'UTF-8') as $chunk) {
                    $wrapped[] = e($chunk);
                }
            }

            return implode('<br>', $wrapped);
        };
    @endphp

    <style>
        @include('trainings.vocabulary.print.pdf._font')

        @page {
            margin: 28px 34px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111827;
            font-size: 12px;
            line-height: 1.75;
        }

        .page-break {
            page-break-after: always;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            border-bottom: 2px solid #111827;
            margin-bottom: 18px;
            padding-bottom: 6px;
        }

        .text-wrap {
            display: block;
            width: 100%;
            max-width: 100%;
            white-space: normal;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        .answer {
            margin-bottom: 24px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 16px;
            width: 100%;
        }

        .head {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .label {
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .answer-text {
            margin-bottom: 6px;
        }

        .scoring-block {
            page-break-inside: avoid;
            margin-bottom: 26px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 16px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #111827;
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        th:first-child,
        td:first-child {
            width: 75%;
        }

        th:last-child,
        td:last-child {
            width: 25%;
        }

        .adjust-note {
            margin-top: 8px;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h1 class="section-title">模範解答</h1>

    @foreach ($printTest->questions as $question)
        <div class="answer">
            <div class="head">
                第{{ $question->question_number }}問　
                {{ $question->question_type }}　
                {{ $question->point }}点
            </div>

            <div class="label">問題</div>
            <div class="answer-text text-wrap">
                {!! $pdfText($question->question_body, 42) !!}
            </div>

            @if (! empty($question->choices_json))
                <div class="label">選択肢</div>
                @foreach ($question->choices_json as $choice)
                    <div class="text-wrap">
                        {!! $pdfText($choice, 42) !!}
                    </div>
                @endforeach

                <div class="label">正解</div>
                <div class="text-wrap">
                    {!! $pdfText($question->correct_choice, 42) !!}
                </div>
            @endif

            <div class="label">模範解答</div>
            <div class="answer-text text-wrap">
                {!! $pdfText($question->answer_text, 42) !!}
            </div>

            @if ($question->explanation_text)
                <div class="label">解説</div>
                <div class="answer-text text-wrap">
                    {!! $pdfText($question->explanation_text, 42) !!}
                </div>
            @endif
        </div>
    @endforeach

    <div class="page-break"></div>

    <h1 class="section-title">採点基準</h1>

    @foreach ($printTest->questions as $question)
        <div class="scoring-block">
            <div class="head">
                第{{ $question->question_number }}問　
                {{ $question->question_type }}　
                {{ $question->point }}点
            </div>

            <table>
                <thead>
                    <tr>
                        <th>採点項目</th>
                        <th>目安点</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (($question->scoring_rule_json ?? []) as $rule)
                        <tr>
                            <td>{!! $pdfText($rule['label'] ?? '', 36) !!}</td>
                            <td>{{ $rule['point'] ?? '' }}点</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="adjust-note text-wrap">
                実際の配点は、この問題の配点 {{ $question->point }}点 に合わせて調整してください。
            </p>
        </div>
    @endforeach
</body>
</html>
