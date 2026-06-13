<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">

    @php
        $pdfText = function (?string $text, int $limit = 40): string {
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

        h1,
        h2,
        h3 {
            margin: 0;
        }

        .text-wrap {
            display: block;
            width: 100%;
            max-width: 100%;
            white-space: normal;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        .cover {
            padding: 40px 30px;
            border: 2px solid #111827;
            min-height: 920px;
        }

        .title {
            font-size: 25px;
            font-weight: bold;
            text-align: center;
            margin-top: 120px;
            letter-spacing: 1px;
        }

        .meta {
            margin: 50px auto 0;
            width: 70%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .meta th,
        .meta td {
            border: 1px solid #111827;
            padding: 9px 10px;
            text-align: left;
            vertical-align: top;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            border-bottom: 2px solid #111827;
            margin-bottom: 18px;
            padding-bottom: 6px;
        }

        .question {
            page-break-inside: avoid;
            margin-bottom: 28px;
            width: 100%;
        }

        .question-head {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .question-body {
            margin-top: 6px;
            margin-bottom: 10px;
        }

        .choices {
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .choice {
            margin-bottom: 2px;
        }

        .answer-lines {
            margin-top: 10px;
            width: 100%;
        }

        .answer-label {
            font-size: 11px;
            margin-bottom: 2px;
        }

        .line {
            border-bottom: 1px solid #999;
            height: 25px;
            width: 100%;
        }

        .small {
            font-size: 11px;
            color: #444;
        }

        .note {
            margin-top: 50px;
            width: 100%;
        }

        .answer-sheet-question {
            page-break-inside: avoid;
            margin-bottom: 22px;
        }
    </style>
</head>
<body>
    <div class="cover">
        <h1 class="title">ボキャブラリー印刷テスト</h1>

        <table class="meta">
            <tr>
                <th>問題数</th>
                <td>{{ $printTest->question_count }}問</td>
            </tr>
            <tr>
                <th>制限時間</th>
                <td>{{ $printTest->time_limit_minutes }}分</td>
            </tr>
            <tr>
                <th>合計点</th>
                <td>{{ $printTest->total_score }}点</td>
            </tr>
            <tr>
                <th>実施日</th>
                <td>　　　　年　　　月　　　日</td>
            </tr>
            <tr>
                <th>名前</th>
                <td>　　　　　　　　　　　　　　</td>
            </tr>
            <tr>
                <th>点数</th>
                <td>　　　　　　　点</td>
            </tr>
        </table>

        <div class="note text-wrap">
            注意：制限時間内に、できるだけ自分の言葉で回答してください。<br>
            問題PDFには答えは含まれていません。解答後に、別ファイルの解答PDFで確認してください。
        </div>
    </div>

    <div class="page-break"></div>

    <h2 class="section-title">問題用紙</h2>

    @foreach ($printTest->questions as $question)
        <div class="question">
            <div class="question-head">
                第{{ $question->question_number }}問　
                {{ $question->question_type }}　
                {{ $question->point }}点
            </div>

            <div class="question-body text-wrap">
                {!! $pdfText($question->question_body, 40) !!}
            </div>

            @if (! empty($question->choices_json))
                <div class="choices">
                    @foreach ($question->choices_json as $choice)
                        <div class="choice text-wrap">
                            {!! $pdfText($choice, 40) !!}
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="answer-lines">
                <div class="answer-label">回答</div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </div>
    @endforeach

    <div class="page-break"></div>

    <h2 class="section-title">解答用紙</h2>

    <table class="meta" style="width: 100%; margin: 0 0 24px;">
        <tr>
            <th>名前</th>
            <td>　　　　　　　　　　　　　　</td>
            <th>実施日</th>
            <td>　　　　年　　　月　　　日</td>
        </tr>
    </table>

    @foreach ($printTest->questions as $question)
        <div class="answer-sheet-question">
            <div class="question-head">
                第{{ $question->question_number }}問　{{ $question->point }}点
            </div>

            <div class="answer-lines">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
        </div>
    @endforeach
</body>
</html>
