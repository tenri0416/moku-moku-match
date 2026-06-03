<x-dynamic-component
    component="errors.layout"
    code="500"
    title="一時的なエラーが発生しました"
    message="申し訳ありません。処理中に問題が発生しました。時間をおいて再度お試しください。"
    detail="入力内容や操作内容に問題がない場合でも、通信状況やサーバー側の処理により一時的に失敗することがあります。"
    illustration="🛠️"
    primaryLabel="トップページへ戻る"
    primaryUrl="{{ url('/') }}"
    secondaryLabel="前のページへ戻る"
    secondaryUrl="{{ url()->previous() }}"
/>
