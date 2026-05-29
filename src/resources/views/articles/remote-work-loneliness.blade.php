@extends('layouts.article')

@section('title', 'フルリモートで孤独を感じる方へ｜一人で仕事が続かない時の対策')

@section('content')
<div class="min-h-screen bg-white">
    <main class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Article Header --}}
        <header class="border-b border-slate-200 pb-8">
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500">
                <span class="rounded-full bg-slate-100 px-3 py-1 font-semibold text-slate-700">
                    フルリモート
                </span>
                <span>在宅勤務</span>
                <span>・</span>
                <span>孤独対策</span>
            </div>

            <h1 class="mt-5 text-3xl font-bold leading-tight tracking-tight text-slate-950 sm:text-4xl">
                フルリモートで孤独を感じるのは、あなたの意志が弱いからではありません
            </h1>

            <p class="mt-5 text-lg leading-8 text-slate-600">
                自宅で一人で働いていると、気分が落ち込んだり、仕事や勉強のモチベーションが続かなくなることがあります。
                この記事では、フルリモートで孤独を感じやすい理由と、一人で抱え込まないための対策を紹介します。
            </p>

            <div class="mt-6 flex flex-wrap gap-3 text-sm text-slate-500">
                <span>公開日：{{ now()->format('Y年n月j日') }}</span>
                <span>・</span>
                <span>読了目安：5分</span>
            </div>
        </header>

        {{-- Lead --}}
        <section class="mt-8 rounded-xl border-l-4 border-indigo-500 bg-slate-50 p-5">
            <p class="leading-8 text-slate-700">
                フルリモートは自由な働き方ですが、同時に「誰にも見られていない」「会話が少ない」「生活と仕事の境目があいまいになる」
                という悩みも起こりやすい働き方です。
            </p>
        </section>

        {{-- Article Body --}}
        <article class="prose prose-slate mt-10 max-w-none prose-h2:mt-12 prose-h2:border-b prose-h2:border-slate-200 prose-h2:pb-3 prose-h2:text-2xl prose-h2:font-bold prose-p:leading-8 prose-li:leading-8">
            <h2>フルリモートで孤独を感じる人は多い</h2>

            <p>
                フルリモートは、通勤がなく、自分のペースで働ける便利な働き方です。
                しかし一方で、毎日自宅で一人で作業していると、人との会話が減り、孤独感を感じやすくなります。
            </p>

            <p>
                特にITエンジニアやフリーランスの場合、チャットでのやり取りはあっても、
                「同じ空間で誰かと作業している感覚」が少なくなりがちです。
            </p>

            <p>
                その結果、仕事を始めるまでに時間がかかったり、集中が続かなかったり、
                気分が落ち込んだときに作業が止まりやすくなることがあります。
            </p>

            <h2>一人で働くと、なぜだらけやすくなるのか</h2>

            <p>
                一人で働いていると、良くも悪くも自分の行動を見ている人がいません。
                そのため、少し休憩するつもりが長くなったり、スマートフォンを見続けてしまったりすることがあります。
            </p>

            <p>
                これは単純に「やる気がない」という話ではありません。
                人は、周囲に人がいるだけで自然と行動を整えやすくなることがあります。
            </p>

            <blockquote>
                <p>
                    誰かに監視される必要はありません。  
                    ただ、同じ時間に作業している人がいるだけで、仕事を始めるきっかけになることがあります。
                </p>
            </blockquote>

            <h2>よくある悩み</h2>

            <p>
                フルリモートで働く人には、次のような悩みが起こりやすいです。
            </p>

            <ul>
                <li>朝から仕事を始めるまでに時間がかかる</li>
                <li>誰にも見られていないため、ついダラダラしてしまう</li>
                <li>勉強を始めても長続きしない</li>
                <li>気分が落ち込むと一気に作業が止まる</li>
                <li>同じ働き方をしている人と話したくなる</li>
            </ul>

            <h2>孤独感を減らすには「誰かと一緒に作業する時間」を作る</h2>

            <p>
                孤独感を完全になくす必要はありません。
                大切なのは、一人で頑張り続ける状態を少しだけ変えることです。
            </p>

            <p>
                たとえば、誰かとオンラインでつないで、最初に「今日はこれをやります」と共有し、
                その後はお互いに黙々と作業するだけでも、仕事を始めやすくなることがあります。
            </p>

            <p>
                このような作業スタイルは「黙々会」と呼ばれることもあります。
                会話をたくさんする必要はありません。
                最初と最後だけ軽く共有し、作業中はそれぞれ集中する形でも十分です。
            </p>

            <h2>黙々会は、フルリモートと相性がよい</h2>

            <p>
                黙々会は、フルリモートで働く人にとって取り入れやすい方法です。
                オンラインでつなぐだけなので、場所を選ばずに始められます。
            </p>

            <p>
                また、会話を目的にするのではなく「一緒に作業すること」を目的にできるため、
                雑談が得意でない人でも参加しやすいのが特徴です。
            </p>

            <h2>MokuMoku Matchでできること</h2>

            <p>
                MokuMoku Matchは、フルリモートや在宅勤務で一人になりがちな人が、
                作業仲間や勉強仲間を探すためのサービスです。
            </p>

            <p>
                たとえば、次のような募集を作成できます。
            </p>

            <ul>
                <li>平日午前に一緒に黙々作業できる方募集</li>
                <li>Laravelの勉強を一緒に進める仲間募集</li>
                <li>フリーランス同士で作業・情報交換したい方募集</li>
                <li>朝の作業習慣を作りたい方募集</li>
            </ul>

            <p>
                最初から深く交流する必要はありません。
                まずは、同じ時間に作業する相手を見つけるだけでも十分です。
            </p>

            <h2>一人で頑張り続けなくても大丈夫です</h2>

            <p>
                フルリモートで孤独を感じるのは、自然なことです。
                気合いや根性だけで解決しようとせず、誰かと一緒に作業する環境を作ってみてください。
            </p>
        </article>

        {{-- CTA --}}
        <section class="mt-12 rounded-2xl border border-slate-200 bg-slate-50 p-6 sm:p-8">
            <h2 class="text-xl font-bold text-slate-950">
                作業仲間を探してみませんか？
            </h2>

            <p class="mt-3 leading-8 text-slate-600">
                MokuMoku Matchでは、フルリモート・在宅勤務・勉強中の方が、
                一緒に作業できる相手を探せます。
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                @auth
                    <a
                        href="{{ route('work-posts.create') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700"
                    >
                        募集を作成する
                    </a>
                @else
                    <a
                        href="{{ route('register') }}"
                        class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-5 py-3 text-sm font-bold text-white transition hover:bg-slate-700"
                    >
                        無料で会員登録する
                    </a>
                @endauth

                <a
                    href="{{ route('work-posts.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100"
                >
                    募集を見る
                </a>
            </div>
        </section>
    </main>
</div>
@endsection
