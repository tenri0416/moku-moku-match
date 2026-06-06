<header class="yw-pc-header">
  <div class="yw-pc-header-inner">
      <a href="{{ route('articles.index') }}" class="yw-pc-logo">
          YomuWorks
      </a>

      <nav class="yw-pc-nav">
          <a href="{{ route('articles.index', ['sort' => 'latest']) }}">
              新着記事
          </a>

          <a href="{{ route('articles.index', ['sort' => 'category']) }}">
              カテゴリー
          </a>
      </nav>

      <div class="yw-pc-header-actions">
          <button type="button" class="yw-pc-search" data-yw-search-open aria-label="検索">
              <span></span>
          </button>

          <a href="{{ route('home') }}" class="yw-pc-newsletter">
              MokuMoku Matchへ
          </a>
      </div>
  </div>

  <div class="yw-search-panel" data-yw-search-panel>
      <form action="{{ route('articles.index') }}" method="GET" class="yw-search-form">
          <input
              type="text"
              name="keyword"
              value="{{ request('keyword') }}"
              placeholder="記事を検索する"
              class="yw-search-input"
          >

          <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}">

          <button type="submit" class="yw-search-submit">
              検索
          </button>

          <button type="button" class="yw-search-close" data-yw-search-close>
              閉じる
          </button>
      </form>
  </div>
</header>
