@extends('layouts.admin')

@section('title', '株式監視')

@section('content')
@php
    $activeWatchlistCount = $watchlists->where('is_active', true)->count();
    $highImportanceCount = $events->filter(
        fn ($event) => $event->analysis?->importance === 'high'
    )->count();

    $lineConfigured = filled($setting->line_user_id)
        || filled(config('stock_alert.line.user_id'));

    $importanceLabels = [
        'high' => 'HIGH',
        'medium' => 'MEDIUM',
        'low' => 'LOW',
    ];

    $importanceClasses = [
        'high' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'medium' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'low' => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];

    $impactLabels = [
        'positive' => 'プラス材料候補',
        'negative' => 'マイナス材料候補',
        'neutral' => '中立',
        'uncertain' => '判断困難',
    ];

    $impactClasses = [
        'positive' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'negative' => 'bg-rose-50 text-rose-700 ring-rose-200',
        'neutral' => 'bg-slate-100 text-slate-600 ring-slate-200',
        'uncertain' => 'bg-slate-100 text-slate-600 ring-slate-200',
    ];
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-7xl px-3 py-6 sm:px-6 sm:py-10 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <p class="text-xs font-bold tracking-wide text-indigo-600 sm:text-sm">
                STOCK MONITORING
            </p>

            <div class="mt-2 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <h1 class="break-words text-2xl font-bold leading-tight text-slate-900 sm:text-3xl">
                        株式監視
                    </h1>

                    <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">
                        指定した銘柄のニュースを定期取得し、AI分析とLINE通知の設定を管理できます。
                    </p>
                </div>

                <details class="group w-full rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 lg:w-[380px] lg:shrink-0">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-bold text-slate-700 outline-none transition hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                        <span class="flex items-center gap-2">
                            <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-xs font-black text-indigo-700 ring-1 ring-indigo-200">?</span>
                            この画面の使い方
                        </span>
                        <span class="text-lg text-slate-400 transition group-open:rotate-180">⌄</span>
                    </summary>

                    <div class="border-t border-slate-200 px-4 py-4 text-xs leading-6 text-slate-600">
                        <ol class="space-y-2">
                            <li><span class="font-bold text-slate-800">1.</span> 「監視銘柄を追加」から証券コードと会社名を登録します。</li>
                            <li><span class="font-bold text-slate-800">2.</span> 「監視ON」の銘柄だけが定期取得の対象になります。</li>
                            <li><span class="font-bold text-slate-800">3.</span> 新しいニュースが見つかるとAIが重要度・材料方向・信頼度を分析します。</li>
                            <li><span class="font-bold text-slate-800">4.</span> LINE通知を有効にすると、条件を満たした重要情報や朝レポートを受け取れます。</li>
                        </ol>

                        {{-- Current specification --}}
                        <div class="mt-4 border-t border-slate-200 pt-4">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-900 text-[11px] font-black text-white">5</span>
                                <p class="font-bold text-slate-900">現在の機能仕様</p>
                            </div>

                            <div class="mt-3 rounded-xl bg-amber-50 p-3 text-amber-900 ring-1 ring-amber-200">
                                <p class="font-bold">最初に知っておくこと</p>
                                <p class="mt-1 leading-5">
                                    現在は「株価そのもの」を監視する機能ではありません。
                                    登録銘柄に関するGoogle Newsの新着情報を定期取得し、AIが重要度や材料方向を分析する機能です。
                                </p>
                            </div>

                            <div class="mt-3 space-y-2">
                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>監視対象・情報源</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <ul class="list-disc space-y-1.5 pl-5">
                                            <li>「監視ON」の銘柄だけを定期確認します。</li>
                                            <li>現在の情報源は <span class="font-bold text-slate-800">Google News RSS</span> です。</li>
                                            <li>証券コードと会社名を使ってニュースを検索し、原則として直近7日以内・1回最大20件を確認します。</li>
                                            <li>購入価格・保有株数は管理用で、現在のAI分析や通知判定には使用しません。</li>
                                        </ul>
                                    </div>
                                </details>

                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>いつ監視する？</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <ul class="list-disc space-y-1.5 pl-5">
                                            <li>平日 <span class="font-bold text-slate-800">08:00〜16:00の1時間ごと</span> に新着ニュースを確認します。</li>
                                            <li>平日 <span class="font-bold text-slate-800">07:15</span> に直近24時間の朝レポートをLINEへ送信します。</li>
                                            <li>現在、土日・祝日を個別判定する市場カレンダー連携はなく、Scheduler上は平日のみ動作します。</li>
                                        </ul>
                                    </div>
                                </details>

                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>AIは何を判断する？</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <ul class="list-disc space-y-1.5 pl-5">
                                            <li>新しく見つかったニュースだけをGroqのAIで分析します。</li>
                                            <li>重要度：<span class="font-bold text-slate-800">HIGH / MEDIUM / LOW</span></li>
                                            <li>材料方向：<span class="font-bold text-slate-800">プラス材料候補 / マイナス材料候補 / 中立 / 判断困難</span></li>
                                            <li>信頼度、影響スコア、要約、理由、注意点も保存します。</li>
                                            <li>AIが追加でWeb検索・IR・EDINET・株価を調査する機能は現在ありません。</li>
                                        </ul>
                                    </div>
                                </details>

                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>LINEはいつ届く？</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <p class="font-bold text-slate-800">即時通知は、次の条件をすべて満たした場合だけ送信します。</p>
                                        <ul class="mt-2 list-disc space-y-1.5 pl-5">
                                            <li>初回スキャンではない新着ニュース</li>
                                            <li>AI重要度が <span class="font-bold text-rose-700">HIGH</span></li>
                                            <li>AI信頼度が <span class="font-bold text-slate-800">60%以上</span></li>
                                            <li>銘柄の「即時通知対象」がON</li>
                                            <li>全体のLINE送信・HIGH即時通知がON</li>
                                        </ul>
                                        <p class="mt-2">MEDIUM / LOWは即時通知せず、朝レポートで確認できます。</p>
                                    </div>
                                </details>

                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>初回・重複・エラー時の動き</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <ul class="list-disc space-y-1.5 pl-5">
                                            <li>初回は既存ニュースを最大5件登録しますが、過去ニュースの大量通知を防ぐため即時LINEは送りません。</li>
                                            <li>同じニュースは重複判定し、原則として再登録・再分析・再通知しません。</li>
                                            <li>AI分析に失敗した場合は「判断困難 / LOW / 信頼度0%」として保存し、他の処理を継続します。</li>
                                            <li>1銘柄のニュース取得に失敗しても、他の銘柄の監視は継続します。</li>
                                        </ul>
                                    </div>
                                </details>

                                <details class="group rounded-xl border border-slate-200 bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5 font-bold text-slate-800 outline-none hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500">
                                        <span>現在できないこと</span>
                                        <span class="text-slate-400 transition group-open:rotate-180">⌄</span>
                                    </summary>
                                    <div class="border-t border-slate-200 px-3 py-3 leading-5 text-slate-600">
                                        <p>現時点では次の情報は取得・判定していません。</p>
                                        <p class="mt-2 font-semibold text-slate-700">
                                            リアルタイム株価 / 現在値 / 前日比 / 騰落率 / 出来高 / PER・PBR / 板情報 / TDnet / EDINET / 企業公式IR / J-Quants / SNS
                                        </p>
                                        <p class="mt-2">そのため「プラス材料候補」「マイナス材料候補」は、現在取得できたニュースを基にしたAI分析です。</p>
                                    </div>
                                </details>
                            </div>

                            <div class="mt-4 rounded-xl bg-slate-50 p-3">
                                <p class="font-bold text-slate-900">よくある疑問</p>
                                <dl class="mt-2 space-y-3 leading-5">
                                    <div>
                                        <dt class="font-bold text-slate-800">Q. 株価が下がったら通知される？</dt>
                                        <dd class="mt-0.5 text-slate-600">A. 現在はされません。株価データ自体を取得していないため、価格変動ではなくニュースを監視しています。</dd>
                                    </div>
                                    <div>
                                        <dt class="font-bold text-slate-800">Q. HIGHなら株価が上がる・下がるという意味？</dt>
                                        <dd class="mt-0.5 text-slate-600">A. いいえ。HIGHは「重要度」です。上昇・下落方向は「材料方向」で別に表示します。</dd>
                                    </div>
                                    <div>
                                        <dt class="font-bold text-slate-800">Q. 購入価格や保有株数はAIが見ている？</dt>
                                        <dd class="mt-0.5 text-slate-600">A. 現在は見ていません。保有状況を管理するための入力項目です。</dd>
                                    </div>
                                    <div>
                                        <dt class="font-bold text-slate-800">Q. 同じニュースが毎時間LINEに来る？</dt>
                                        <dd class="mt-0.5 text-slate-600">A. 原則来ません。同一ニュースを重複判定して再分析・再通知を防止しています。</dd>
                                    </div>
                                    <div>
                                        <dt class="font-bold text-slate-800">Q. n8nやAI Agentは必要？</dt>
                                        <dd class="mt-0.5 text-slate-600">A. 現在は不要です。Laravel Schedulerが定期実行し、LaravelからGroqを直接呼び出しています。</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="mt-3 rounded-xl bg-rose-50 p-3 text-rose-800 ring-1 ring-rose-200">
                                <p class="font-bold">AI分析について</p>
                                <p class="mt-1 leading-5">
                                    AIは将来の株価や売買成果を保証するものではありません。ニュース材料を整理する補助情報として利用してください。
                                </p>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </div>

        {{-- Flash message --}}
        @if (session('status'))
            <div class="mb-6 rounded-2xl bg-emerald-50 px-4 py-4 text-sm font-semibold leading-6 text-emerald-800 ring-1 ring-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 px-4 py-4 ring-1 ring-rose-200">
                <p class="text-sm font-bold text-rose-800">
                    入力内容を確認してください。
                </p>

                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm leading-6 text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Summary --}}
        <div class="mb-6 grid grid-cols-2 gap-3 sm:mb-8 sm:grid-cols-4 sm:gap-4">
            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-bold text-slate-500 sm:text-sm">監視銘柄</p>
                    <span class="group relative inline-flex">
                        <button type="button" aria-label="監視銘柄の説明" class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-black text-slate-500 outline-none ring-1 ring-slate-200 hover:bg-slate-200 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                        <span role="tooltip" class="pointer-events-none absolute left-0 top-full z-40 mt-2 hidden w-56 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                            登録されている監視銘柄の総数です。監視停止中の銘柄も含みます。
                        </span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">{{ $watchlists->count() }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-bold text-slate-500 sm:text-sm">監視中</p>
                    <span class="group relative inline-flex">
                        <button type="button" aria-label="監視中の説明" class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-black text-slate-500 outline-none ring-1 ring-slate-200 hover:bg-slate-200 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                        <span role="tooltip" class="pointer-events-none absolute left-0 top-full z-40 mt-2 hidden w-56 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                            「監視ON」になっていて、定期ニュース取得・AI分析の対象になっている銘柄数です。
                        </span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black text-emerald-600 sm:text-3xl">{{ $activeWatchlistCount }}</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-bold text-slate-500 sm:text-sm">HIGH情報</p>
                    <span class="group relative inline-flex">
                        <button type="button" aria-label="HIGH情報の説明" class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-black text-slate-500 outline-none ring-1 ring-slate-200 hover:bg-slate-200 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                        <span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-40 mt-2 hidden w-64 -translate-x-1/2 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                            最新50件の取得情報のうち、AIが重要度「HIGH」と判定した件数です。株価上昇・下落を保証する指標ではありません。
                        </span>
                    </span>
                </div>
                <p class="mt-2 text-2xl font-black text-rose-600 sm:text-3xl">{{ $highImportanceCount }}</p>
                <p class="mt-1 text-[11px] text-slate-400">最新50件内</p>
            </div>

            <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                <div class="flex items-center gap-1.5">
                    <p class="text-xs font-bold text-slate-500 sm:text-sm">LINE通知</p>
                    <span class="group relative inline-flex">
                        <button type="button" aria-label="LINE通知の説明" class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-100 text-[10px] font-black text-slate-500 outline-none ring-1 ring-slate-200 hover:bg-slate-200 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                        <span role="tooltip" class="pointer-events-none absolute right-0 top-full z-40 mt-2 hidden w-64 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                            LINE送信設定と送信先の両方が有効な場合に「有効」と表示します。
                        </span>
                    </span>
                </div>
                <p class="mt-2 text-base font-black sm:text-lg {{ $setting->line_enabled && $lineConfigured ? 'text-emerald-600' : 'text-slate-500' }}">
                    {{ $setting->line_enabled && $lineConfigured ? '有効' : '無効' }}
                </p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            {{-- LINE Settings --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 sm:text-xl">
                            LINE通知設定
                        </h2>
                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            重要ニュースの即時通知と朝レポートを設定します。
                        </p>
                    </div>

                    @if ($lineConfigured)
                        <span class="inline-flex w-fit items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">
                            LINE設定済み
                        </span>
                    @else
                        <span class="inline-flex w-fit items-center rounded-full bg-rose-50 px-3 py-1 text-xs font-bold text-rose-700 ring-1 ring-rose-200">
                            LINE未設定
                        </span>
                    @endif
                </div>

                @if (! $lineConfigured)
                    <div class="mt-4 rounded-xl bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-800 ring-1 ring-amber-200">
                        <code class="font-bold">LINE_ADMIN_TO</code> が設定されていません。
                        LINE通知を利用する場合は既存のLINE送信先設定を確認してください。
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.stocks.settings.update') }}" class="mt-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <input
                                type="checkbox"
                                name="line_enabled"
                                value="1"
                                @checked($setting->line_enabled)
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span>
                                <span class="block text-sm font-bold text-slate-900">
                                    LINE送信を有効化
                                </span>
                                <span class="mt-1 block text-xs leading-5 text-slate-500">
                                    Stock AlertからLINEへの通知送信を有効にします。
                                </span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <input
                                type="checkbox"
                                name="immediate_alert_enabled"
                                value="1"
                                @checked($setting->immediate_alert_enabled)
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span>
                                <span class="block text-sm font-bold text-slate-900">
                                    HIGH重要度を即時通知
                                </span>
                                <span class="mt-1 block text-xs leading-5 text-slate-500">
                                    AIが重要度HIGHと判定した新着情報をLINEへ即時送信します。
                                </span>
                            </span>
                        </label>

                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-4 transition hover:bg-slate-50">
                            <input
                                type="checkbox"
                                name="morning_summary_enabled"
                                value="1"
                                @checked($setting->morning_summary_enabled)
                                class="mt-1 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span>
                                <span class="block text-sm font-bold text-slate-900">
                                    朝レポートを送信
                                </span>
                                <span class="mt-1 block text-xs leading-5 text-slate-500">
                                    監視銘柄の直近情報を朝にまとめてLINEへ送信します。
                                </span>
                            </span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700 sm:w-auto"
                    >
                        通知設定を保存
                    </button>
                </form>
            </section>

            {{-- Add Watchlist --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 sm:text-xl">
                        監視銘柄を追加
                    </h2>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        証券コードと会社名を登録すると、定期監視の対象になります。
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.stocks.store') }}" class="mt-5">
                    @csrf

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="stock_code" class="block text-sm font-bold text-slate-700">
                                証券コード
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="stock_code"
                                type="text"
                                name="stock_code"
                                maxlength="4"
                                required
                                value="{{ old('stock_code') }}"
                                placeholder="例：1431 / 130A"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                4文字の数字または英数字
                            </p>
                        </div>

                        <div>
                            <label for="company_name" class="block text-sm font-bold text-slate-700">
                                会社名
                                <span class="text-rose-500">*</span>
                            </label>
                            <input
                                id="company_name"
                                type="text"
                                name="company_name"
                                required
                                value="{{ old('company_name') }}"
                                placeholder="例：Lib Work"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >
                        </div>

                        <div>
                            <label for="purchase_price" class="block text-sm font-bold text-slate-700">
                                購入価格
                            </label>
                            <input
                                id="purchase_price"
                                type="number"
                                name="purchase_price"
                                step="0.01"
                                min="0"
                                value="{{ old('purchase_price') }}"
                                placeholder="例：720"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >
                        </div>

                        <div>
                            <label for="shares" class="block text-sm font-bold text-slate-700">
                                保有株数
                            </label>
                            <input
                                id="shares"
                                type="number"
                                name="shares"
                                min="1"
                                step="1"
                                value="{{ old('shares') }}"
                                placeholder="例：100"
                                class="mt-2 w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 sm:w-auto"
                    >
                        監視銘柄を追加
                    </button>
                </form>
            </section>
        </div>

        {{-- Watchlists --}}
        <section class="mt-6 sm:mt-8">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-slate-900 sm:text-xl">監視銘柄</h2>
                        <span class="group relative inline-flex">
                            <button type="button" aria-label="監視銘柄設定の説明" class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs font-black text-slate-500 outline-none ring-1 ring-slate-300 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                            <span role="tooltip" class="pointer-events-none absolute left-0 top-full z-40 mt-2 hidden w-72 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                                「監視ON」は定期取得の対象、「即時通知対象」は重要ニュースをLINEへ即時通知する対象です。購入価格・保有株数は任意の管理情報です。
                            </span>
                        </span>
                    </div>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        登録済み銘柄の監視状態、購入情報、通知対象を変更できます。
                    </p>
                </div>

                <p class="text-xs leading-5 text-slate-400">
                    画面幅が狭い場合はカード表示へ自動で切り替わります。
                </p>
            </div>

            {{-- Compact cards: MacBook 13-inch / tablet / mobile --}}
            <div class="grid grid-cols-1 gap-4 min-[900px]:grid-cols-2 min-[1440px]:hidden">
                @forelse ($watchlists as $watchlist)
                    <article class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-xs font-bold tracking-wide text-indigo-600">{{ $watchlist->stock_code }}</p>
                                <h3 class="mt-1 truncate text-base font-bold leading-6 text-slate-900" title="{{ $watchlist->company_name }}">
                                    {{ $watchlist->company_name }}
                                </h3>
                            </div>

                            @if ($watchlist->is_active)
                                <span class="shrink-0 whitespace-nowrap rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700 ring-1 ring-emerald-200">監視中</span>
                            @else
                                <span class="shrink-0 whitespace-nowrap rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">停止中</span>
                            @endif
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                            <div class="min-w-0">
                                <span class="block font-bold text-slate-500">購入価格</span>
                                <span class="mt-1 block truncate text-sm font-semibold text-slate-900">
                                    {{ $watchlist->purchase_price !== null ? number_format((float) $watchlist->purchase_price, 2) . ' 円' : '-' }}
                                </span>
                            </div>
                            <div class="min-w-0">
                                <span class="block font-bold text-slate-500">保有株数</span>
                                <span class="mt-1 block truncate text-sm font-semibold text-slate-900">
                                    {{ $watchlist->shares !== null ? number_format($watchlist->shares) . ' 株' : '-' }}
                                </span>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-500">初期化</span>
                                <span class="mt-1 block whitespace-nowrap text-slate-700">
                                    {{ $watchlist->initialized_at?->format('Y/m/d H:i') ?? '未実行' }}
                                </span>
                            </div>
                            <div>
                                <span class="block font-bold text-slate-500">最終確認</span>
                                <span class="mt-1 block whitespace-nowrap text-slate-700">
                                    {{ $watchlist->last_checked_at?->format('Y/m/d H:i') ?? '-' }}
                                </span>
                            </div>
                        </div>

                        @if ($watchlist->last_error)
                            <div class="mt-3 break-words rounded-xl bg-rose-50 px-3 py-3 text-xs leading-5 text-rose-700 ring-1 ring-rose-200">
                                <span class="font-bold">最終エラー：</span>{{ $watchlist->last_error }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.stocks.update', $watchlist) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="company_name_{{ $watchlist->id }}" class="block text-xs font-bold text-slate-600">会社名</label>
                                <input
                                    id="company_name_{{ $watchlist->id }}"
                                    type="text"
                                    name="company_name"
                                    required
                                    value="{{ $watchlist->company_name }}"
                                    class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                >
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="min-w-0">
                                    <label for="purchase_price_{{ $watchlist->id }}" class="block text-xs font-bold text-slate-600">購入価格</label>
                                    <input
                                        id="purchase_price_{{ $watchlist->id }}"
                                        type="number"
                                        name="purchase_price"
                                        step="0.01"
                                        min="0"
                                        value="{{ $watchlist->purchase_price }}"
                                        placeholder="未設定"
                                        class="mt-2 w-full min-w-0 rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    >
                                </div>

                                <div class="min-w-0">
                                    <label for="shares_{{ $watchlist->id }}" class="block text-xs font-bold text-slate-600">保有株数</label>
                                    <input
                                        id="shares_{{ $watchlist->id }}"
                                        type="number"
                                        name="shares"
                                        min="1"
                                        step="1"
                                        value="{{ $watchlist->shares }}"
                                        placeholder="未設定"
                                        class="mt-2 w-full min-w-0 rounded-xl border border-slate-300 px-3 py-3 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                    >
                                </div>
                            </div>

                            <div class="grid gap-2 sm:grid-cols-2">
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                    <input type="checkbox" name="is_active" value="1" @checked($watchlist->is_active) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="whitespace-nowrap">監視ON</span>
                                </label>

                                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                    <input type="checkbox" name="notify_news" value="1" @checked($watchlist->notify_news) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="whitespace-nowrap">即時通知対象</span>
                                </label>
                            </div>

                            <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-slate-700">
                                設定を保存
                            </button>
                        </form>

                        <form
                            method="POST"
                            action="{{ route('admin.stocks.destroy', $watchlist) }}"
                            class="mt-3"
                            onsubmit="return confirm('「{{ $watchlist->stock_code }} {{ $watchlist->company_name }}」を監視対象から削除しますか？');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex w-full items-center justify-center rounded-xl border border-rose-200 bg-white px-4 py-3 text-sm font-bold text-rose-600 transition hover:bg-rose-50">削除</button>
                        </form>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl bg-white px-4 py-10 text-center shadow-sm ring-1 ring-slate-200">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">📈</div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">監視銘柄はありません</h3>
                        <p class="mt-2 text-sm text-slate-600">上のフォームから監視したい銘柄を追加してください。</p>
                    </div>
                @endforelse
            </div>

            {{-- Wide desktop table --}}
            <div class="hidden overflow-visible rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 min-[1440px]:block">
                <div class="overflow-x-auto rounded-2xl">
                    <table class="w-full table-fixed divide-y divide-slate-200">
                        <colgroup>
                            <col style="width: 13%">
                            <col style="width: 23%">
                            <col style="width: 19%">
                            <col style="width: 17%">
                            <col style="width: 17%">
                            <col style="width: 11%">
                        </colgroup>
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">銘柄</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">会社名</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">保有情報</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">監視・通知</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">状態</th>
                                <th class="whitespace-nowrap px-4 py-4 text-right text-xs font-bold uppercase tracking-wide text-slate-500">操作</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($watchlists as $watchlist)
                                <tr class="align-top hover:bg-slate-50">
                                    <td class="px-4 py-4">
                                        <p class="whitespace-nowrap text-base font-black text-slate-900">{{ $watchlist->stock_code }}</p>
                                        <p class="mt-1 text-[11px] leading-5 text-slate-500">
                                            初期化<br>
                                            <span class="whitespace-nowrap">{{ $watchlist->initialized_at?->format('Y/m/d H:i') ?? '未実行' }}</span>
                                        </p>
                                    </td>

                                    <td class="min-w-0 px-4 py-4">
                                        <form method="POST" action="{{ route('admin.stocks.update', $watchlist) }}" id="watchlist-form-{{ $watchlist->id }}">
                                            @csrf
                                            @method('PUT')
                                            <input
                                                type="text"
                                                name="company_name"
                                                required
                                                value="{{ $watchlist->company_name }}"
                                                title="{{ $watchlist->company_name }}"
                                                class="w-full min-w-0 truncate rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                            >
                                        </form>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="grid gap-2">
                                            <label class="grid grid-cols-[64px_minmax(0,1fr)] items-center gap-2 text-[11px] font-bold text-slate-500">
                                                <span class="whitespace-nowrap">購入価格</span>
                                                <input
                                                    form="watchlist-form-{{ $watchlist->id }}"
                                                    type="number"
                                                    name="purchase_price"
                                                    step="0.01"
                                                    min="0"
                                                    value="{{ $watchlist->purchase_price }}"
                                                    placeholder="未設定"
                                                    class="w-full min-w-0 rounded-lg border border-slate-300 px-2.5 py-2 text-sm font-normal text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                                >
                                            </label>
                                            <label class="grid grid-cols-[64px_minmax(0,1fr)] items-center gap-2 text-[11px] font-bold text-slate-500">
                                                <span class="whitespace-nowrap">保有株数</span>
                                                <input
                                                    form="watchlist-form-{{ $watchlist->id }}"
                                                    type="number"
                                                    name="shares"
                                                    min="1"
                                                    step="1"
                                                    value="{{ $watchlist->shares }}"
                                                    placeholder="未設定"
                                                    class="w-full min-w-0 rounded-lg border border-slate-300 px-2.5 py-2 text-sm font-normal text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                                >
                                            </label>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="space-y-2">
                                            <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap text-xs font-semibold text-slate-700">
                                                <input form="watchlist-form-{{ $watchlist->id }}" type="checkbox" name="is_active" value="1" @checked($watchlist->is_active) class="h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                監視ON
                                            </label>
                                            <label class="flex cursor-pointer items-center gap-2 whitespace-nowrap text-xs font-semibold text-slate-700">
                                                <input form="watchlist-form-{{ $watchlist->id }}" type="checkbox" name="notify_news" value="1" @checked($watchlist->notify_news) class="h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                即時通知対象
                                            </label>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-xs leading-5 text-slate-600">
                                        <div class="flex flex-wrap gap-1.5">
                                            @if ($watchlist->is_active)
                                                <span class="whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 font-bold text-emerald-700 ring-1 ring-emerald-200">監視中</span>
                                            @else
                                                <span class="whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 font-bold text-slate-600 ring-1 ring-slate-200">停止中</span>
                                            @endif

                                            @if ($watchlist->notify_news)
                                                <span class="whitespace-nowrap rounded-full bg-indigo-50 px-2.5 py-1 font-bold text-indigo-700 ring-1 ring-indigo-200">通知対象</span>
                                            @endif
                                        </div>

                                        <p class="mt-2 whitespace-nowrap text-[11px]">最終確認：{{ $watchlist->last_checked_at?->format('m/d H:i') ?? '-' }}</p>

                                        @if ($watchlist->last_error)
                                            <p class="mt-2 line-clamp-2 break-words rounded-lg bg-rose-50 px-2.5 py-2 text-[11px] text-rose-700 ring-1 ring-rose-200" title="{{ $watchlist->last_error }}">
                                                {{ $watchlist->last_error }}
                                            </p>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-right">
                                        <div class="flex flex-col items-stretch gap-2">
                                            <button form="watchlist-form-{{ $watchlist->id }}" type="submit" class="inline-flex items-center justify-center whitespace-nowrap rounded-lg bg-indigo-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-indigo-700">
                                                保存
                                            </button>

                                            <form method="POST" action="{{ route('admin.stocks.destroy', $watchlist) }}" onsubmit="return confirm('「{{ $watchlist->stock_code }} {{ $watchlist->company_name }}」を監視対象から削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex w-full items-center justify-center whitespace-nowrap rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">削除</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">📈</div>
                                        <h3 class="mt-4 text-lg font-bold text-slate-900">監視銘柄はありません</h3>
                                        <p class="mt-2 text-sm text-slate-600">上のフォームから監視したい銘柄を追加してください。</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Latest Events --}}
        <section class="mt-6 sm:mt-8">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-slate-900 sm:text-xl">最新の取得情報</h2>
                        <span class="group relative inline-flex">
                            <button type="button" aria-label="AI分析項目の説明" class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-xs font-black text-slate-500 outline-none ring-1 ring-slate-300 hover:bg-slate-100 focus-visible:ring-2 focus-visible:ring-indigo-500">?</button>
                            <span role="tooltip" class="pointer-events-none absolute left-0 top-full z-40 mt-2 hidden w-80 rounded-xl bg-slate-900 px-3 py-2 text-xs font-normal leading-5 text-white shadow-lg group-hover:block group-focus-within:block">
                                HIGH/MEDIUM/LOWはAIによる重要度、材料方向は企業価値・株価材料としての方向性候補です。信頼度やScoreはAI分析用の内部指標であり、将来の株価を保証するものではありません。
                            </span>
                        </span>
                    </div>
                    <p class="mt-1 text-sm leading-6 text-slate-500">
                        取得したニュースとAI分析結果を新しい順に最大50件表示します。
                    </p>
                </div>

                <p class="text-xs leading-5 text-slate-400">
                    ニュースタイトルをクリックすると取得元を新しいタブで開きます。
                </p>
            </div>

            {{-- Compact event cards: MacBook 13-inch / tablet / mobile --}}
            <div class="grid grid-cols-1 gap-4 min-[1000px]:grid-cols-2 min-[1440px]:hidden">
                @forelse ($events as $event)
                    @php
                        $analysis = $event->analysis;
                        $importance = $analysis?->importance ?? 'low';
                        $impact = $analysis?->impact ?? 'uncertain';
                    @endphp

                    <article class="min-w-0 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-bold text-indigo-600" title="{{ $event->watchlist->stock_code }} / {{ $event->watchlist->company_name }}">
                                    {{ $event->watchlist->stock_code }}
                                    <span class="text-slate-400">/</span>
                                    {{ $event->watchlist->company_name }}
                                </p>
                                <p class="mt-1 whitespace-nowrap text-xs text-slate-500">
                                    {{ $event->published_at?->format('Y/m/d H:i') ?? $event->created_at->format('Y/m/d H:i') }}
                                </p>
                            </div>

                            @if ($analysis)
                                <span class="shrink-0 whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $importanceClasses[$importance] ?? $importanceClasses['low'] }}">
                                    {{ $importanceLabels[$importance] ?? strtoupper($importance) }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 min-w-0">
                            @if ($event->source_url)
                                <a href="{{ $event->source_url }}" target="_blank" rel="noopener noreferrer" class="line-clamp-3 break-words text-sm font-bold leading-6 text-slate-900 underline decoration-slate-300 underline-offset-4 transition hover:text-indigo-700" title="{{ $event->title }}">
                                    {{ $event->title }}
                                </a>
                            @else
                                <p class="line-clamp-3 break-words text-sm font-bold leading-6 text-slate-900" title="{{ $event->title }}">{{ $event->title }}</p>
                            @endif

                            <p class="mt-2 truncate text-xs text-slate-500" title="{{ $event->source ?: '取得元不明' }}">
                                {{ $event->source ?: '取得元不明' }}
                            </p>
                        </div>

                        <div class="mt-4 border-t border-slate-100 pt-4">
                            @if ($analysis)
                                <div class="flex flex-wrap gap-2">
                                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $impactClasses[$impact] ?? $impactClasses['uncertain'] }}">
                                        {{ $impactLabels[$impact] ?? $impact }}
                                    </span>
                                    <span class="whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">信頼度 {{ $analysis->confidence }}%</span>
                                    <span class="whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 ring-1 ring-slate-200">Score {{ $analysis->impact_score }}</span>
                                </div>

                                <p class="mt-3 line-clamp-4 break-words text-sm leading-6 text-slate-700" title="{{ $analysis->summary }}">
                                    {{ $analysis->summary }}
                                </p>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">未分析</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl bg-white px-4 py-10 text-center shadow-sm ring-1 ring-slate-200">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">📰</div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">取得情報はありません</h3>
                        <p class="mt-2 text-sm text-slate-600">株式監視を実行すると、取得したニュースとAI分析結果がここに表示されます。</p>
                    </div>
                @endforelse
            </div>

            {{-- Wide desktop event table --}}
            <div class="hidden overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 min-[1440px]:block">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed divide-y divide-slate-200">
                        <colgroup>
                            <col style="width: 14%">
                            <col style="width: 18%">
                            <col style="width: 38%">
                            <col style="width: 30%">
                        </colgroup>
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">日時</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">銘柄</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">ニュース</th>
                                <th class="whitespace-nowrap px-4 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500">AI分析</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($events as $event)
                                @php
                                    $analysis = $event->analysis;
                                    $importance = $analysis?->importance ?? 'low';
                                    $impact = $analysis?->impact ?? 'uncertain';
                                @endphp

                                <tr class="align-top hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-500">
                                        {{ $event->published_at?->format('Y/m/d H:i') ?? $event->created_at->format('Y/m/d H:i') }}
                                    </td>

                                    <td class="min-w-0 px-4 py-4">
                                        <p class="whitespace-nowrap text-sm font-black text-slate-900">{{ $event->watchlist->stock_code }}</p>
                                        <p class="mt-1 truncate text-xs leading-5 text-slate-500" title="{{ $event->watchlist->company_name }}">{{ $event->watchlist->company_name }}</p>
                                    </td>

                                    <td class="min-w-0 px-4 py-4">
                                        @if ($event->source_url)
                                            <a href="{{ $event->source_url }}" target="_blank" rel="noopener noreferrer" class="line-clamp-2 break-words text-sm font-bold leading-6 text-slate-900 underline decoration-slate-300 underline-offset-4 transition hover:text-indigo-700" title="{{ $event->title }}">
                                                {{ $event->title }}
                                            </a>
                                        @else
                                            <p class="line-clamp-2 break-words text-sm font-bold leading-6 text-slate-900" title="{{ $event->title }}">{{ $event->title }}</p>
                                        @endif
                                        <p class="mt-2 truncate text-xs text-slate-500" title="{{ $event->source ?: '取得元不明' }}">{{ $event->source ?: '取得元不明' }}</p>
                                    </td>

                                    <td class="min-w-0 px-4 py-4">
                                        @if ($analysis)
                                            <div class="flex flex-wrap gap-1.5">
                                                <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $importanceClasses[$importance] ?? $importanceClasses['low'] }}">{{ $importanceLabels[$importance] ?? strtoupper($importance) }}</span>
                                                <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-bold ring-1 {{ $impactClasses[$impact] ?? $impactClasses['uncertain'] }}">{{ $impactLabels[$impact] ?? $impact }}</span>
                                            </div>

                                            <p class="mt-3 line-clamp-3 break-words text-sm leading-6 text-slate-700" title="{{ $analysis->summary }}">{{ $analysis->summary }}</p>
                                            <p class="mt-2 whitespace-nowrap text-xs text-slate-500">信頼度 {{ $analysis->confidence }}% <span class="mx-1">/</span> Score {{ $analysis->impact_score }}</p>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">未分析</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-12 text-center">
                                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">📰</div>
                                        <h3 class="mt-4 text-lg font-bold text-slate-900">取得情報はありません</h3>
                                        <p class="mt-2 text-sm text-slate-600">株式監視を実行すると、取得したニュースとAI分析結果がここに表示されます。</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="mt-6 sm:mt-8">
            <a
                href="{{ route('admin.dashboard') }}"
                class="text-sm font-bold text-slate-600 transition hover:text-slate-900"
            >
                管理者ダッシュボードへ戻る
            </a>
        </div>
    </div>
</div>
@endsection
