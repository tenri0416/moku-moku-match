<footer class="yw-sp-footer">
  <p class="yw-sp-footer-logo">YomuWorks</p>

  <nav class="yw-sp-footer-nav">
      <a href="{{ route('articles.index', ['sort' => 'latest']) }}">
          新着記事
      </a>

      <a href="{{ route('articles.index', ['sort' => 'category']) }}">
          カテゴリー
      </a>

      <a href="{{ route('home') }}">
          MokuMoku Match
      </a>

      <button type="button" class="yw-footer-contact-button" data-yw-contact-open>
          お問い合わせ
      </button>
  </nav>

  <div class="yw-sp-footer-links">
      <a href="{{ route('home') }}">MokuMoku Matchへ</a>
  </div>

  <p class="yw-sp-footer-copy">
      © {{ date('Y') }} YomuWorks. All rights reserved.
  </p>
</footer>

@include('articles.contact-modal')
