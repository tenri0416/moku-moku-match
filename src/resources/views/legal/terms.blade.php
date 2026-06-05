@extends('layouts.app')

@section('title', '利用規約｜MokuMoku Match')

@section('content')
@php
    $serviceName = 'MokuMoku Match';
    $domain = 'https://mokumokumatch.top';
    $effectiveDate = '2026年6月5日';
@endphp

<div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm font-bold tracking-wide text-indigo-600">TERMS OF SERVICE</p>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                利用規約
            </h1>
            <p class="mt-5 max-w-3xl leading-8 text-slate-600">
                本利用規約は、{{ $serviceName }}（以下「本サービス」といいます。）の利用条件を定めるものです。
                ユーザーは、本サービスを利用することにより、本規約に同意したものとみなされます。
            </p>
            <div class="mt-6 rounded-2xl bg-slate-50 px-5 py-4 text-sm font-semibold leading-7 text-slate-600 ring-1 ring-slate-200">
                <p>制定日：{{ $effectiveDate }}</p>
                <p>対象URL：{{ $domain }}</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-10">
            <div class="space-y-10 leading-8 text-slate-700">
                <section>
                    <h2 class="text-xl font-black text-slate-900">1. 適用</h2>
                    <p class="mt-4">
                        本規約は、本サービスの利用に関する運営者とユーザーとの間の一切の関係に適用されます。
                        本サービスに個別のガイドライン、注意事項、ヘルプ等がある場合、それらも本規約の一部を構成します。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">2. サービス内容</h2>
                    <p class="mt-4">
                        本サービスは、フルリモートで働くITエンジニア、フリーランス、学習者等が、作業仲間、勉強仲間、情報交換相手を探し、
                        募集投稿、参加申請、メッセージ、プロフィール閲覧、自己成長トレーニング等を利用できるWebサービスです。
                    </p>
                    <p class="mt-4">
                        本サービスは、作業・学習・情報交換・自己成長を目的としたサービスであり、恋愛、出会い、投資勧誘、営業勧誘、違法行為を目的とした利用を禁止します。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">3. アカウント登録</h2>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>ユーザーは、正確かつ最新の情報を登録するものとします。</li>
                        <li>登録情報に変更があった場合、ユーザーは速やかに修正するものとします。</li>
                        <li>虚偽の情報、第三者の情報、なりすましによる登録を禁止します。</li>
                        <li>Googleログイン等の外部認証を利用する場合、当該外部サービスの利用規約にも従うものとします。</li>
                        <li>運営者は、登録情報に不備または不正があると判断した場合、登録を拒否またはアカウントを停止できるものとします。</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">4. アカウント管理</h2>
                    <p class="mt-4">
                        ユーザーは、自己の責任においてログイン情報を管理するものとします。
                        ログイン情報の管理不十分、第三者の使用、不正アクセス等により生じた損害について、運営者は運営者の故意または重過失がある場合を除き責任を負いません。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">5. 投稿・メッセージ・プロフィール</h2>
                    <p class="mt-4">
                        ユーザーは、募集投稿、プロフィール、メッセージ、トレーニング入力、コメント、通報等において、自己の責任で内容を投稿または送信するものとします。
                    </p>
                    <p class="mt-4">
                        運営者は、投稿内容が本規約に違反する、または不適切であると判断した場合、事前通知なく削除、非公開化、利用制限、アカウント停止等の措置を行うことができます。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">6. AI機能の利用</h2>
                    <p class="mt-4">
                        本サービスでは、トレーニングの問題生成、採点、アドバイス生成等にAI機能を利用する場合があります。
                        AIによる出力は、必ずしも正確性、完全性、有用性、適法性を保証するものではありません。
                    </p>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>AIの採点結果、アドバイス、模範解答は参考情報として利用してください。</li>
                        <li>ユーザーは、個人情報、機密情報、第三者の権利を侵害する情報を入力しないものとします。</li>
                        <li>AIサービスの障害、利用上限、外部APIエラー等により、AI機能が一時的に利用できない場合があります。</li>
                        <li>AI機能が利用できない場合、本サービスは簡易採点、固定問題、フォールバック処理等に切り替える場合があります。</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">7. ポイント・ランキング</h2>
                    <p class="mt-4">
                        本サービスは、トレーニングの継続を支援するため、ポイント、ランキング、難易度、バッジ等の機能を提供する場合があります。
                        これらは本サービス内での利用を目的としたものであり、金銭的価値、換金性、財産的価値を有しません。
                    </p>
                    <p class="mt-4">
                        運営者は、不正利用、システム不具合、集計ミス等が確認された場合、ポイントやランキングを修正、取消、非表示にできるものとします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">8. 禁止事項</h2>
                    <p class="mt-4">ユーザーは、本サービスの利用にあたり、以下の行為をしてはなりません。</p>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>法令または公序良俗に反する行為</li>
                        <li>犯罪行為、違法行為、またはそれらを助長する行為</li>
                        <li>第三者になりすます行為、虚偽情報を登録・投稿する行為</li>
                        <li>他のユーザー、第三者、運営者の権利、名誉、信用、プライバシーを侵害する行為</li>
                        <li>差別、誹謗中傷、脅迫、嫌がらせ、ストーカー行為</li>
                        <li>恋愛、出会い、性的目的、宗教勧誘、政治活動、マルチ商法、投資勧誘、営業勧誘を主目的とした利用</li>
                        <li>スパム、広告、宣伝、勧誘、外部サービスへの不適切な誘導</li>
                        <li>不正アクセス、過度なリクエスト、スクレイピング、リバースエンジニアリング、システム負荷を与える行為</li>
                        <li>AI機能を悪用した不正生成、権利侵害、迷惑行為、セキュリティ回避行為</li>
                        <li>本サービスの運営を妨害する行為</li>
                        <li>その他、運営者が不適切と判断する行為</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">9. 利用停止・登録抹消</h2>
                    <p class="mt-4">
                        運営者は、ユーザーが本規約に違反した場合、または運営者が必要と判断した場合、事前通知なく、投稿削除、機能制限、利用停止、アカウント削除等の措置を行うことができます。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">10. 退会</h2>
                    <p class="mt-4">
                        ユーザーは、運営者所定の方法により退会できるものとします。
                        退会後も、法令上保存が必要な情報、不正利用防止のために必要な情報、すでに公開・送信された情報の一部が保存される場合があります。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">11. 知的財産権</h2>
                    <p class="mt-4">
                        本サービスに関するデザイン、プログラム、文章、画像、ロゴ、商標、その他一切の知的財産権は、運営者または正当な権利者に帰属します。
                        ユーザーは、権利者の許可なくこれらを複製、転載、改変、販売、配布してはなりません。
                    </p>
                    <p class="mt-4">
                        ユーザーが本サービスに投稿した内容の権利は、原則として当該ユーザーに帰属します。
                        ただし、ユーザーは、運営者が本サービスの提供、表示、運営、改善、広報、SEO対策に必要な範囲で、投稿内容を利用することを許諾するものとします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">12. サービスの変更・停止・終了</h2>
                    <p class="mt-4">
                        運営者は、ユーザーへの事前通知なく、本サービスの内容を変更、追加、停止、終了することがあります。
                        これによりユーザーに生じた損害について、運営者は運営者の故意または重過失がある場合を除き責任を負いません。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">13. 免責事項</h2>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>本サービスは、ユーザー同士の交流、作業、学習、自己成長を支援するものであり、成果、出会い、契約成立、学習効果等を保証しません。</li>
                        <li>ユーザー間のトラブル、メッセージ、募集、参加申請、外部でのやり取りについて、ユーザー自身の責任で対応するものとします。</li>
                        <li>AIの出力内容、採点結果、アドバイスについて、正確性、完全性、最新性、有用性を保証しません。</li>
                        <li>通信障害、システム障害、外部サービス障害、第三者による不正アクセス等により生じた損害について、運営者は運営者の故意または重過失がある場合を除き責任を負いません。</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">14. 損害賠償</h2>
                    <p class="mt-4">
                        ユーザーが本規約に違反し、運営者または第三者に損害を与えた場合、ユーザーはその損害を賠償する責任を負うものとします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">15. 規約の変更</h2>
                    <p class="mt-4">
                        運営者は、必要に応じて本規約を変更できるものとします。
                        重要な変更がある場合は、本サービス上での掲示その他適切な方法により通知します。
                        変更後にユーザーが本サービスを利用した場合、変更後の規約に同意したものとみなします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">16. 準拠法・管轄</h2>
                    <p class="mt-4">
                        本規約の解釈には日本法を準拠法とします。
                        本サービスに関して紛争が生じた場合、運営者の所在地を管轄する日本の裁判所を第一審の専属的合意管轄裁判所とします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">17. お問い合わせ</h2>
                    <p class="mt-4">
                        本規約に関するお問い合わせは、本サービスのお問い合わせ窓口または運営者が指定する連絡先までお願いいたします。
                    </p>
                    <div class="mt-4 rounded-2xl bg-slate-50 p-5 text-sm font-semibold leading-7 text-slate-600 ring-1 ring-slate-200">
                        <p>サービス名：{{ $serviceName }}</p>
                        <p>URL：{{ $domain }}</p>
                        <p>お問い合わせ：本サービス内のお問い合わせ窓口</p>
                    </div>
                </section>
            </div>
        </div>
    </section>
</div>
@endsection
