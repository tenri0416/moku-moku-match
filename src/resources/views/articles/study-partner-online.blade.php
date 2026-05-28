@extends('layouts.app')

@section('title', '一緒に勉強する相手をオンラインで探す方法｜続かない勉強を習慣にする')

@section('content')
<div class="min-h-screen bg-[#fbf7ff]">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="rounded-[2rem] bg-white p-6 shadow-sm ring-1 ring-purple-100 sm:p-10">
            <header>
                <p class="inline-flex rounded-full bg-purple-100 px-4 py-2 text-sm font-black text-purple-700">
                    STUDY PARTNER
                </p>

                <h1 class="mt-5 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">
                    一緒に勉強する相手がいるだけで、勉強は少し続けやすくなる。
                </h1>

                <p class="mt-5 leading-8 text-slate-700">
                    勉強が続かない理由は、やる気がないからとは限りません。
                    一人で始め、一人で悩み、一人で終わる状態が長く続くと、勉強は止まりやすくなります。
                </p>
            </header>

            <div class="mt-10 space-y-10">
                <section>
                    <h2 class="text-2xl font-black text-slate-950">勉強は「始めるまで」が一番重い</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        勉強そのものより、机に向かうまでが大変なことがあります。
                        特に仕事終わりや休日は、少し休むつもりがそのまま時間だけ過ぎてしまうこともあります。
                    </p>
                    <p class="mt-4 leading-8 text-slate-700">
                        一緒に勉強する相手がいると、開始時間が決まりやすくなります。
                        「今日は何をやるか」を一言共有するだけでも、勉強に入るきっかけになります。
                    </p>
                </section>

                <section class="rounded-3xl bg-purple-50 p-6">
                    <h2 class="text-xl font-black text-purple-950">一緒に勉強するときのおすすめルール</h2>
                    <ul class="mt-4 space-y-3 leading-8 text-purple-950">
                        <li>・最初に今日やることを一言だけ共有する</li>
                        <li>・作業中は無理に話さない</li>
                        <li>・時間は30分〜60分から始める</li>
                        <li>・最後にできたことを一言だけ共有する</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">同じ分野でなくてもよい</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        一緒に勉強する相手は、必ずしも同じ教材や同じ技術を学んでいる必要はありません。
                        Laravelを勉強する人と、英語を勉強する人が同じ時間に作業しても問題ありません。
                    </p>
                    <p class="mt-4 leading-8 text-slate-700">
                        大切なのは、同じ時間に集中することです。
                        内容が違っても、開始と終了を共有するだけで習慣化しやすくなります。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">オンラインなら小さく始められる</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        オンラインなら、移動せずに始められます。
                        顔出しが苦手な場合は、音声だけ、チャットだけ、作業報告だけでも十分です。
                    </p>
                </section>

                <footer class="rounded-2xl border border-purple-100 bg-slate-50 p-6">
                    <p class="leading-8 text-slate-700">
                        一緒に勉強する相手を探したい場合は、勉強仲間や作業仲間を募集できる場所を使うと始めやすいです。
                        MokuMoku Matchでも、オンラインで一緒に勉強する相手を探せます。
                    </p>
                    <a href="{{ route('work-posts.index') }}" class="mt-4 inline-flex text-sm font-bold text-purple-700 underline underline-offset-4">
                        勉強仲間の募集を見る
                    </a>
                </footer>
            </div>
        </article>
    </main>
</div>
@endsection
