<footer class="yw-pc-footer">
  <div class="yw-pc-footer-inner">
      <p class="yw-pc-footer-logo">YomuWorks</p>

      <nav class="yw-pc-footer-nav">
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

      <p class="yw-pc-footer-text">
          技術、個人開発、暮らし、働き方を整える読みものメディア。
      </p>

      <p class="yw-pc-footer-copy">
          © {{ date('Y') }} YomuWorks.
      </p>
  </div>
</footer>

@include('articles.contact-modal')
