@extends('layouts.app')

@section('title', 'プライバシーポリシー｜MokuMoku Match')

@section('content')
@php
    $serviceName = 'MokuMoku Match';
    $domain = 'https://mokumokumatch.top';
    $effectiveDate = '2026年6月5日';
@endphp

<div class="min-h-screen bg-slate-50 text-slate-900">
    <section class="border-b border-slate-200 bg-white">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:px-6 lg:px-8">
            <p class="text-sm font-bold tracking-wide text-indigo-600">PRIVACY POLICY</p>
            <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
                プライバシーポリシー
            </h1>
            <p class="mt-5 max-w-3xl leading-8 text-slate-600">
                {{ $serviceName }}（以下「本サービス」といいます。）は、ユーザーの個人情報を適切に取り扱うことを重要な責務と考え、
                本プライバシーポリシーに基づき、個人情報の取得、利用、管理を行います。
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
                    <h2 class="text-xl font-black text-slate-900">1. 取得する情報</h2>
                    <p class="mt-4">
                        本サービスは、サービス提供、本人確認、問い合わせ対応、利便性向上、不正利用防止のため、以下の情報を取得する場合があります。
                    </p>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>氏名、表示名、メールアドレス、パスワードその他アカウント登録に必要な情報</li>
                        <li>Googleログイン等の外部認証サービスから提供される識別子、氏名、メールアドレス、プロフィール情報</li>
                        <li>プロフィール情報、職種、スキル、自己紹介、目的、作業スタイル、プロフィール画像</li>
                        <li>募集投稿、参加申請、メッセージ、通報、問い合わせ等、ユーザーが本サービス上で入力または送信した情報</li>
                        <li>日記、今日のチャレンジ、要約力、言語化力、抽象化力、具体化力などのトレーニング入力内容、採点結果、AIによるアドバイス、獲得ポイント、ランキングに関する情報</li>
                        <li>ログイン履歴、アクセス日時、IPアドレス、ユーザーエージェント、参照元URL、操作ログ、エラーログ</li>
                        <li>Cookie、端末情報、ブラウザ情報、アクセス解析に関する情報</li>
                        <li>電話番号、生年月日、住所等、将来的な本人確認や問い合わせ対応のためにユーザーが任意で登録する情報</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">2. 利用目的</h2>
                    <p class="mt-4">本サービスは、取得した情報を以下の目的で利用します。</p>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>本サービスの登録、本人確認、ログイン、アカウント管理のため</li>
                        <li>Googleログイン等の外部認証によるログイン機能を提供するため</li>
                        <li>募集投稿、参加申請、メッセージ、プロフィール表示、ランキング表示等の機能を提供するため</li>
                        <li>日記・チャレンジ・AI出題型トレーニングの保存、採点、アドバイス、ポイント付与、成長可視化のため</li>
                        <li>不正利用、迷惑行為、規約違反、セキュリティ上の問題を検知・防止するため</li>
                        <li>問い合わせ、通報、障害対応、本人確認、重要なお知らせの連絡のため</li>
                        <li>サービス改善、新機能開発、利用状況分析、品質向上のため</li>
                        <li>アクセス解析、SEO改善、広告効果測定、マーケティング分析のため</li>
                        <li>法令または公的機関からの要請に対応するため</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">3. Googleログインについて</h2>
                    <p class="mt-4">
                        本サービスでは、Googleアカウントを利用したログイン機能を提供する場合があります。
                        Googleログインを利用する場合、本サービスはGoogleから提供されるユーザー識別子、氏名、メールアドレス等を取得し、
                        アカウント作成、ログイン、本人確認のために利用します。
                    </p>
                    <p class="mt-4">
                        Googleログインによって取得した情報は、本サービスの認証およびユーザー管理の目的に限って利用し、
                        ユーザーの同意なく不要な第三者提供を行いません。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">4. AI機能における情報の取扱い</h2>
                    <p class="mt-4">
                        本サービスでは、トレーニングの問題作成、採点、アドバイス生成等のためにAIサービスを利用する場合があります。
                        ユーザーが入力した文章は、採点やアドバイス生成のため、Google AI、OpenRouter、Groq等の外部AIサービスへ送信される場合があります。
                    </p>
                    <p class="mt-4">
                        ユーザーは、個人を特定できる情報、機密情報、第三者の個人情報、業務上の秘密情報をトレーニング入力欄に記載しないよう注意してください。
                        本サービスは、AI処理の安定化、品質改善、不正利用防止のため、AIの利用状況、成功・失敗状況、エラー内容等を記録する場合があります。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">5. Cookie・アクセス解析について</h2>
                    <p class="mt-4">
                        本サービスでは、ログイン状態の維持、セキュリティ対策、利用状況の分析、サービス改善のためにCookieを利用する場合があります。
                        また、アクセス解析ツールを利用し、ページ閲覧数、流入元、利用端末、ブラウザ等の情報を取得する場合があります。
                    </p>
                    <p class="mt-4">
                        Cookieの利用を希望しない場合、ユーザーはブラウザの設定によりCookieを無効化できます。
                        ただし、Cookieを無効化した場合、本サービスの一部機能を利用できない場合があります。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">6. 第三者提供</h2>
                    <p class="mt-4">
                        本サービスは、次の場合を除き、ユーザーの同意なく個人情報を第三者に提供しません。
                    </p>
                    <ul class="mt-4 list-disc space-y-2 pl-6">
                        <li>法令に基づく場合</li>
                        <li>人の生命、身体または財産の保護のために必要がある場合</li>
                        <li>公的機関から正当な要請を受けた場合</li>
                        <li>不正利用、権利侵害、セキュリティ上の問題に対応するために必要な場合</li>
                        <li>外部認証、AI処理、メール送信、アクセス解析、インフラ運用等、本サービスの提供に必要な範囲で外部サービスを利用する場合</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">7. 外部サービスの利用</h2>
                    <p class="mt-4">
                        本サービスは、サービス提供のため、Google、AIサービス、メール配信サービス、アクセス解析サービス、クラウド・ホスティングサービス等の外部サービスを利用する場合があります。
                        各外部サービスに送信された情報は、各サービス提供事業者の規約およびプライバシーポリシーに従って取り扱われます。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">8. 情報の管理</h2>
                    <p class="mt-4">
                        本サービスは、取得した情報について、不正アクセス、紛失、破壊、改ざん、漏えい等を防止するため、合理的な安全管理措置を講じます。
                        ただし、インターネット上の通信やシステム運用において、完全な安全性を保証するものではありません。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">9. 情報の保存期間</h2>
                    <p class="mt-4">
                        本サービスは、利用目的の達成に必要な範囲で情報を保存します。
                        退会、利用停止、保存期間の経過、または運営上不要と判断した場合、法令上保存が必要な情報を除き、情報を削除または匿名化する場合があります。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">10. ユーザーによる確認・修正・削除</h2>
                    <p class="mt-4">
                        ユーザーは、本サービス上で登録情報を確認、修正できる場合があります。
                        個人情報の開示、訂正、利用停止、削除等を希望する場合は、本サービスのお問い合わせ窓口よりご連絡ください。
                        本人確認を行ったうえで、法令に従い合理的な範囲で対応します。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">11. 未成年の利用</h2>
                    <p class="mt-4">
                        未成年のユーザーが本サービスを利用する場合は、親権者等の法定代理人の同意を得たうえで利用してください。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">12. プライバシーポリシーの変更</h2>
                    <p class="mt-4">
                        本サービスは、必要に応じて本プライバシーポリシーを変更することがあります。
                        重要な変更がある場合は、本サービス上での掲示その他適切な方法により通知します。
                        変更後に本サービスを利用した場合、変更後の内容に同意したものとみなします。
                    </p>
                </section>

                <section>
                    <h2 class="text-xl font-black text-slate-900">13. お問い合わせ</h2>
                    <p class="mt-4">
                        本プライバシーポリシーに関するお問い合わせは、本サービスのお問い合わせ窓口または運営者が指定する連絡先までお願いいたします。
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
