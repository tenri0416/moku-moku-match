@extends('layouts.app')

@section('title', 'フルリモートで仲間を作る方法｜仕事以外のつながりを小さく増やす')

@section('content')
<div class="min-h-screen bg-[#fff8f1]">
    <main class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <article class="overflow-hidden rounded-[2rem] bg-white shadow-sm ring-1 ring-orange-100">
            <header class="bg-gradient-to-br from-orange-100 to-white p-6 sm:p-10">
                <p class="text-sm font-black tracking-widest text-orange-700">
                    REMOTE CONNECTION
                </p>

                <h1 class="mt-4 text-3xl font-black leading-tight text-slate-950 sm:text-4xl">
                    フルリモートで仲間を作るには、いきなり深く関わらなくていい。
                </h1>

                <p class="mt-5 leading-8 text-slate-700">
                    フルリモートでは、仕事の自由度が高い一方で、自然な出会いや雑談が減りやすくなります。
                    この記事では、無理なく人とのつながりを作る方法を紹介します。
                </p>
            </header>

            <div class="space-y-10 p-6 sm:p-10">
                <section>
                    <h2 class="text-2xl font-black text-slate-950">会社の雑談は、意外と大きな役割を持っている</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        出社しているときは、休憩中の会話やちょっとした相談が自然に発生します。
                        その小さなやり取りが、気分転換や安心感につながることがあります。
                    </p>
                    <p class="mt-4 leading-8 text-slate-700">
                        フルリモートでは、その偶然の接点が減ります。
                        そのため、自分から小さな接点を作る工夫が必要になります。
                    </p>
                </section>

                <section class="rounded-3xl border border-orange-200 bg-orange-50 p-6">
                    <h2 class="text-xl font-black text-orange-950">おすすめは「目的のあるつながり」</h2>
                    <p class="mt-4 leading-8 text-orange-950">
                        いきなり友達を作ろうとするとハードルが高くなります。
                        それよりも、作業、勉強、朝活、情報交換のように、目的があるつながりの方が始めやすいです。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">仲間作りは、雑談よりも作業から始める</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        雑談が苦手な人でも、同じ時間に作業することなら始めやすいです。
                        最初は「今日はこれをやります」と共有して、それぞれ作業するだけでも構いません。
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-slate-950">つながりは少なくていい</h2>
                    <p class="mt-4 leading-8 text-slate-700">
                        毎日多くの人と関わる必要はありません。
                        週に1回でも、同じ働き方の人と話したり、作業時間を共有したりするだけで気分が変わることがあります。
                    </p>
                </section>

                <footer class="rounded-2xl border border-orange-100 bg-slate-50 p-6">
                    <p class="leading-8 text-slate-700">
                        フルリモートで作業仲間を探したい場合は、オンラインで募集できる場所を使うのも一つの方法です。
                        MokuMoku Matchでは、作業や勉強を一緒に進める相手を探せます。
                    </p>
                    <a href="{{ route('work-posts.index') }}" class="mt-4 inline-flex text-sm font-bold text-orange-700 underline underline-offset-4">
                        作業仲間の募集を見る
                    </a>
                </footer>
            </div>
        </article>
    </main>
</div>
@endsection
