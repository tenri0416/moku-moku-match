@extends('layouts.article')

@section('title', '一人だと作業が続かない理由｜在宅ワークで集中するための小さな工夫')

@section('content')
<div class="min-h-screen bg-[#f4fbf7]">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="rounded-[2rem] border border-emerald-100 bg-white p-6 sm:p-10">
            <header class="relative overflow-hidden rounded-3xl bg-emerald-900 p-7 text-white">
                <p class="text-sm font-black tracking-widest text-emerald-200">
                    WORK ROUTINE
                </p>

                <h1 class="mt-4 text-3xl font-black leading-tight sm:text-4xl">
                    一人だと作業が続かないのは、仕組みが足りないだけかもしれません。
                </h1>

                <p class="mt-5 leading-8 text-emerald-50">
                    在宅ワークや個人開発では、誰にも見られていない時間が長くなります。
                    その状態で集中し続けるには、気合いよりも小さな仕組みが必要です。
                </p>
            </header>

            <div class="mt-10 space-y-10 text-slate-800">
                <section>
                    <h2 class="text-2xl font-black text-slate-950">人は「開始の合図」がないと止まりやすい</h2>
                    <p class="mt-4 leading-8">
                        会社や学校では、始業時間や授業時間が自然な合図になります。
                        しかし一人で作業していると、その合図を自分で作る必要があります。
                    </p>
                    <p class="mt-4 leading-8">
                        合図がないと、少し休むつもりが長くなったり、作業を始めるタイミングを逃したりします。
                    </p>
                </section>

                <section class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-emerald-50 p-5">
                        <p class="text-sm font-bold text-emerald-700">STEP 1</p>
                        <p class="mt-2 font-black">時間を決める</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-5">
                        <p class="text-sm font-bold text-emerald-700">STEP 2</p>
                        <p class="mt-2 font-black">やることを一つに絞る</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-5">
                        <p class="text-sm font-bold text-emerald-700">STEP 3</p>
                        <p class="mt-2 font-black">誰かに開始を共有する</p>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">作業を続けるには、完璧な集中はいらない</h2>
                    <p class="mt-4 leading-8">
                        毎日長時間集中しようとすると、続けること自体が苦しくなります。
                        最初は25分だけ、30分だけでも十分です。
                    </p>
                    <p class="mt-4 leading-8">
                        大切なのは、長く頑張ることではなく、作業を始める回数を増やすことです。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">誰かと同じ時間に作業する</h2>
                    <p class="mt-4 leading-8">
                        一人だと作業が続かない場合、誰かと同じ時間に作業するだけでリズムが作りやすくなります。
                        会話をしなくても、開始と終了を共有するだけで十分です。
                    </p>
                </section>

                <footer class="rounded-2xl bg-emerald-50 p-6">
                    <p class="leading-8">
                        作業のリズムを作りたい場合は、黙々作業の相手を探してみるのも一つの方法です。
                        MokuMoku Matchでは、時間を合わせて作業できる相手を探せます。
                    </p>
                    <a href="{{ route('work-posts.index') }}" class="mt-4 inline-flex text-sm font-bold text-emerald-800 underline underline-offset-4">
                        作業仲間を探す
                    </a>
                </footer>
            </div>
        </article>
    </main>
</div>
@endsection
