@extends('layouts.article')

@section('title', 'オンライン黙々会とは？話さなくても一緒に作業できる時間の作り方')

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="rounded-[2rem] border border-slate-700 bg-slate-900 p-6 shadow-xl sm:p-10">
            <header class="border-b border-slate-700 pb-8">
                <p class="text-sm font-bold tracking-[0.3em] text-cyan-300">
                    QUIET WORK SESSION
                </p>

                <h1 class="mt-4 text-3xl font-black leading-tight sm:text-4xl">
                    オンライン黙々会とは？話さなくても一緒に作業できる時間の作り方
                </h1>

                <p class="mt-5 leading-8 text-slate-300">
                    黙々会は、誰かと同じ時間に集まり、それぞれの作業を進める時間です。
                    会話が得意でなくても参加しやすく、フルリモートや勉強中の人と相性のよい方法です。
                </p>
            </header>

            <div class="mt-10 space-y-10">
                <section>
                    <h2 class="text-2xl font-black text-white">黙々会は、雑談会ではない</h2>
                    <p class="mt-4 leading-8 text-slate-300">
                        オンライン黙々会は、長く話すことを目的にした集まりではありません。
                        最初に今日やることを一言共有し、その後は各自が静かに作業します。
                    </p>
                    <p class="mt-4 leading-8 text-slate-300">
                        最後に、できたことを軽く共有するだけでも十分です。
                        そのため、初対面の人とも始めやすいのが特徴です。
                    </p>
                </section>

                <section class="rounded-2xl border border-cyan-400/40 bg-cyan-400/10 p-5">
                    <h2 class="text-lg font-black text-cyan-200">基本の流れ</h2>
                    <ol class="mt-4 space-y-3 leading-8 text-slate-200">
                        <li>1. 開始時間にオンラインで集まる</li>
                        <li>2. 今日やることを一言ずつ共有する</li>
                        <li>3. 30分〜90分ほど各自で作業する</li>
                        <li>4. 最後に進捗を一言共有する</li>
                    </ol>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-white">なぜ集中しやすくなるのか</h2>
                    <p class="mt-4 leading-8 text-slate-300">
                        一人で作業していると、始めるタイミングを自分だけで作る必要があります。
                        しかし、誰かと開始時間を合わせるだけで、自然と作業に入りやすくなります。
                    </p>
                    <p class="mt-4 leading-8 text-slate-300">
                        監視される必要はありません。
                        「同じ時間に作業している人がいる」という感覚が、作業開始のきっかけになります。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-white">向いている人</h2>
                    <ul class="mt-5 grid gap-3 sm:grid-cols-2">
                        <li class="rounded-xl bg-slate-800 p-4">一人だと作業開始が遅れる人</li>
                        <li class="rounded-xl bg-slate-800 p-4">在宅勤務で孤独を感じる人</li>
                        <li class="rounded-xl bg-slate-800 p-4">勉強を続けたい人</li>
                        <li class="rounded-xl bg-slate-800 p-4">雑談より作業時間がほしい人</li>
                    </ul>
                </section>

                <footer class="rounded-2xl border border-slate-700 bg-slate-800 p-6">
                    <p class="leading-8 text-slate-300">
                        オンラインで黙々会の相手を探したい場合は、作業仲間を募集できる場所を使うと始めやすいです。
                        MokuMoku Matchでも、黙々作業の募集を探せます。
                    </p>
                    <a href="{{ route('work-posts.index') }}" class="mt-4 inline-flex text-sm font-bold text-cyan-300 underline underline-offset-4">
                        黙々作業の募集を見る
                    </a>
                </footer>
            </div>
        </article>
    </main>
</div>
@endsection
