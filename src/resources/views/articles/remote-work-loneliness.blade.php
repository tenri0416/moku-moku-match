@extends('layouts.app')

@section('title', 'フルリモートで孤独を感じる方へ｜一人で仕事が続かない時の対策')

@section('content')
<div class="min-h-screen bg-slate-50">
    {{-- Hero --}}
    <section class="bg-gradient-to-br from-indigo-600 via-blue-600 to-sky-500">
        <div class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="inline-flex rounded-full bg-white/15 px-4 py-2 text-sm font-bold text-white ring-1 ring-white/30">
                    フルリモート・在宅勤務の孤独感に悩む方へ
                </p>

                <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl">
                    フルリモートで孤独を感じるのは、あなたの意志が弱いからではありません。
                </h1>

                <p class="mt-6 text-lg leading-8 text-blue-50">
                    自宅で一人で働いていると、気分が落ち込んだり、仕事や勉強のモチベーションが続かなくなることがあります。
                    それは決して珍しいことではありません。
                </p>

                <div class="mt-8 flex flex-wrap gap-4">
                    @auth
                        <a
                            href="{{ route('work-posts.index') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-indigo-900/20 transition hover:bg-indigo-50"
                        >
                            作業仲間を探す
                        </a>

                        <a
                            href="{{ route('work-posts.create') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >
                            募集を作成する
                        </a>
                    @else
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 shadow-lg shadow-indigo-900/20 transition hover:bg-indigo-50"
                        >
                            無料で会員登録する
                        </a>

                        <a
                            href="{{ route('work-posts.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >
                            募集を見る
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- Article --}}
    <main class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-3">
            {{-- Main Content --}}
            <article class="space-y-8 lg:col-span-2">
                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-2xl font-black text-slate-900">
                        フルリモートで孤独を感じる人は多い
                    </h2>

                    <div class="mt-5 space-y-4 leading-8 text-slate-700">
                        <p>
                            フルリモートは、通勤がなく、自分のペースで働ける便利な働き方です。
                            しかし一方で、毎日自宅で一人で作業していると、人との会話が減り、孤独感を感じやすくなります。
                        </p>

                        <p>
                            特にITエンジニアやフリーランスの場合、チャットでのやり取りはあっても、
                            「同じ空間で誰かと作業している感覚」が少なくなりがちです。
                        </p>

                        <p>
                            その結果、次のような悩みが出てくることがあります。
                        </p>

                        <ul class="space-y-3 rounded-2xl bg-slate-50 p-5">
                            <li>・朝から仕事を始めるまでに時間がかかる</li>
                            <li>・誰にも見られていないため、ついダラダラしてしまう</li>
                            <li>・勉強を始めても長続きしない</li>
                            <li>・気分が落ち込むと一気に作業が止まる</li>
                            <li>・同じ働き方をしている人と話したくなる</li>
                        </ul>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-2xl font-black text-slate-900">
                        孤独感を減らすには「誰かと一緒に作業する時間」を作る
                    </h2>

                    <div class="mt-5 space-y-4 leading-8 text-slate-700">
                        <p>
                            孤独感を完全になくす必要はありません。
                            大切なのは、一人で頑張り続ける状態を少しだけ変えることです。
                        </p>

                        <p>
                            たとえば、誰かとオンラインでつないで、最初に「今日はこれをやります」と共有し、
                            その後はお互いに黙々と作業するだけでも、集中しやすくなることがあります。
                        </p>

                        <p>
                            これは一般的に「黙々会」と呼ばれることもあります。
                            会話をたくさんする必要はありません。
                            むしろ、作業中は話さず、最初と最後だけ軽く共有する形でも十分です。
                        </p>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-2xl font-black text-slate-900">
                        MokuMoku Matchでできること
                    </h2>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-indigo-50 p-5">
                            <div class="text-2xl">💻</div>
                            <h3 class="mt-3 font-bold text-indigo-950">
                                黙々作業の相手を探せる
                            </h3>
                            <p class="mt-2 text-sm leading-7 text-indigo-900">
                                ZoomやGoogle Meetなどでつないで、一緒に作業する相手を探せます。
                            </p>
                        </div>

                        <div class="rounded-2xl bg-sky-50 p-5">
                            <div class="text-2xl">📚</div>
                            <h3 class="mt-3 font-bold text-sky-950">
                                勉強仲間を探せる
                            </h3>
                            <p class="mt-2 text-sm leading-7 text-sky-900">
                                Laravel、React、AWSなど、同じ技術を学ぶ人とつながれます。
                            </p>
                        </div>

                        <div class="rounded-2xl bg-emerald-50 p-5">
                            <div class="text-2xl">🤝</div>
                            <h3 class="mt-3 font-bold text-emerald-950">
                                同じ働き方の人と出会える
                            </h3>
                            <p class="mt-2 text-sm leading-7 text-emerald-900">
                                フリーランスやフルリモートの働き方について話せる相手を探せます。
                            </p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-5">
                            <div class="text-2xl">⏱️</div>
                            <h3 class="mt-3 font-bold text-amber-950">
                                作業習慣を作りやすい
                            </h3>
                            <p class="mt-2 text-sm leading-7 text-amber-900">
                                決まった時間に誰かと作業することで、仕事や勉強を始めやすくなります。
                            </p>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                    <h2 class="text-2xl font-black text-slate-900">
                        こんな使い方がおすすめです
                    </h2>

                    <div class="mt-5 space-y-4 leading-8 text-slate-700">
                        <p>
                            MokuMoku Matchでは、以下のような募集を作成できます。
                        </p>

                        <div class="space-y-3">
                            <div class="rounded-xl border border-slate-200 p-4">
                                <p class="font-bold text-slate-900">
                                    平日午前に一緒に黙々作業できる方募集
                                </p>
                                <p class="mt-2 text-sm text-slate-600">
                                    朝の作業開始を習慣化したい方におすすめです。
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4">
                                <p class="font-bold text-slate-900">
                                    Laravelの勉強を一緒に進める仲間募集
                                </p>
                                <p class="mt-2 text-sm text-slate-600">
                                    同じ技術を勉強している人とつながりたい方におすすめです。
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4">
                                <p class="font-bold text-slate-900">
                                    フリーランス同士で作業・情報交換したい方募集
                                </p>
                                <p class="mt-2 text-sm text-slate-600">
                                    案件や働き方について話せる相手がほしい方におすすめです。
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl bg-indigo-600 p-6 text-white shadow-sm sm:p-8">
                    <h2 class="text-2xl font-black">
                        一人で頑張り続けなくても大丈夫です
                    </h2>

                    <p class="mt-4 leading-8 text-indigo-50">
                        フルリモートで孤独を感じるのは、自然なことです。
                        気合いや根性だけで解決しようとせず、誰かと一緒に作業する環境を作ってみてください。
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        @auth
                            <a
                                href="{{ route('work-posts.create') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50"
                            >
                                募集を作成する
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-white px-6 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50"
                            >
                                無料で会員登録する
                            </a>
                        @endauth

                        <a
                            href="{{ route('work-posts.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-white/40 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/10"
                        >
                            募集を見る
                        </a>
                    </div>
                </section>
            </article>

            {{-- Sidebar --}}
            <aside class="space-y-6">
                <section class="sticky top-24 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-black text-slate-900">
                        この記事の対象者
                    </h2>

                    <ul class="mt-4 space-y-3 text-sm leading-7 text-slate-600">
                        <li>・フルリモートで孤独を感じている方</li>
                        <li>・在宅勤務でモチベーションが上がらない方</li>
                        <li>・一人だと勉強が続かない方</li>
                        <li>・黙々会や作業仲間を探している方</li>
                        <li>・フリーランスで人との接点が少ない方</li>
                    </ul>

                    <div class="mt-6 border-t border-slate-200 pt-6">
                        @auth
                            <a
                                href="{{ route('work-posts.create') }}"
                                class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                募集を作成する
                            </a>
                        @else
                            <a
                                href="{{ route('register') }}"
                                class="flex w-full items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-indigo-700"
                            >
                                無料で始める
                            </a>
                        @endauth

                        <a
                            href="{{ route('work-posts.index') }}"
                            class="mt-3 flex w-full items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50"
                        >
                            募集を見る
                        </a>
                    </div>
                </section>
            </aside>
        </div>
    </main>
</div>
@endsection
