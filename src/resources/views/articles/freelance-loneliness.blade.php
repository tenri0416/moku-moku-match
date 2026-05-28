@extends('layouts.app')

@section('title', 'フリーランスが孤独を感じる理由｜一人で働く不安との付き合い方')

@section('content')
<div class="min-h-screen bg-[#f8f5ef]">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="rounded-[2rem] border border-stone-300 bg-white p-6 shadow-sm sm:p-10">
            <header class="border-b border-dashed border-stone-300 pb-8">
                <p class="text-sm font-bold tracking-widest text-stone-500">
                    FREELANCE NOTE
                </p>

                <h1 class="mt-4 text-3xl font-black leading-tight text-stone-950 sm:text-4xl">
                    フリーランスが孤独を感じるのは、仕事が嫌いだからではない。
                </h1>

                <p class="mt-5 leading-8 text-stone-700">
                    フリーランスは自由な働き方です。けれど、自由であるほど、誰にも相談できない時間が増えることがあります。
                    この記事では、フリーランスが孤独を感じやすい理由と、無理なく人との接点を作る方法を整理します。
                </p>

                <div class="mt-6 flex flex-wrap gap-2 text-xs font-bold text-stone-500">
                    <span class="rounded-full bg-stone-100 px-3 py-1">フリーランス</span>
                    <span class="rounded-full bg-stone-100 px-3 py-1">孤独</span>
                    <span class="rounded-full bg-stone-100 px-3 py-1">在宅ワーク</span>
                </div>
            </header>

            <div class="mt-10 space-y-10 text-stone-800">
                <section>
                    <h2 class="text-2xl font-black text-stone-950">
                        孤独は「人がいない」ことだけで起きるわけではない
                    </h2>

                    <p class="mt-4 leading-8">
                        フリーランスの孤独は、単に一人で作業しているから起きるとは限りません。
                        むしろ大きいのは、仕事の進め方、悩み、失敗、将来の不安を共有する相手が少ないことです。
                    </p>

                    <p class="mt-4 leading-8">
                        会社員であれば、ちょっとした雑談や朝会の中で、自然に自分の状態を確認できます。
                        しかしフリーランスの場合、自分で仕事を取り、自分で進め、自分で判断する場面が多くなります。
                    </p>
                </section>

                <aside class="rounded-2xl border-l-4 border-amber-400 bg-amber-50 p-5">
                    <p class="text-sm font-bold text-amber-900">小さな観察メモ</p>
                    <p class="mt-2 leading-7 text-amber-950">
                        孤独を感じる日は、作業量が少ない日ではなく「誰とも進捗を共有していない日」であることがあります。
                    </p>
                </aside>

                <section>
                    <h2 class="text-2xl font-black text-stone-950">
                        一人で働くと、仕事の区切りが見えにくい
                    </h2>

                    <p class="mt-4 leading-8">
                        フリーランスは、仕事の開始時間も終了時間も自分で決められます。
                        それは大きなメリットですが、同時に「今日はここまで」という区切りが曖昧になりやすい働き方でもあります。
                    </p>

                    <p class="mt-4 leading-8">
                        区切りがないと、休んでいても仕事のことを考え続けたり、逆に仕事を始めるタイミングを失ったりします。
                        その積み重ねが、孤独感や焦りにつながることがあります。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-stone-950">
                        孤独をなくすより、接点を小さく作る
                    </h2>

                    <p class="mt-4 leading-8">
                        孤独を完全になくそうとすると、逆に疲れてしまいます。
                        まずは、毎日深く話す相手を作るより、週に数回だけでも同じ時間に作業する相手を作る方が現実的です。
                    </p>

                    <ul class="mt-5 space-y-3 rounded-2xl bg-stone-50 p-5 leading-8">
                        <li>・朝だけ誰かと作業開始を共有する</li>
                        <li>・週1回だけオンラインで黙々作業する</li>
                        <li>・同じ技術を勉強している人と進捗を話す</li>
                        <li>・作業後に一言だけ成果を共有する</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-stone-950">
                        最初から仲良くなる必要はない
                    </h2>

                    <p class="mt-4 leading-8">
                        人との接点というと、雑談や交流を想像しがちです。
                        しかし、フリーランスに必要なのは必ずしも深い交流ではありません。
                    </p>

                    <p class="mt-4 leading-8">
                        「同じ時間に作業している人がいる」と感じられるだけで、仕事を始めるきっかけになることがあります。
                        まずは、会話よりも作業を中心にした関係から始めるのも一つの方法です。
                    </p>
                </section>

                <footer class="rounded-2xl border border-stone-200 bg-stone-50 p-6">
                    <p class="text-sm font-bold text-stone-500">関連する選択肢</p>
                    <p class="mt-3 leading-8 text-stone-700">
                        オンラインで一緒に作業する相手を探したい場合は、作業仲間を募集できるサービスを使う方法もあります。
                        MokuMoku Matchでは、フリーランスや在宅ワーカー向けに、黙々作業の相手を探せます。
                    </p>

                    <a href="{{ route('work-posts.index') }}" class="mt-4 inline-flex text-sm font-bold text-stone-900 underline underline-offset-4">
                        募集を見てみる
                    </a>
                </footer>
            </div>
        </article>
    </main>
</div>
@endsection
