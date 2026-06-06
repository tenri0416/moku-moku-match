<header class="yw-sp-header">
  <a href="{{ route('articles.index', ['sort' => 'latest']) }}" class="yw-sp-header-link">
      新着
  </a>

  <a href="{{ route('articles.index') }}" class="yw-sp-logo">
      YomuWorks
  </a>

  <button type="button" class="yw-sp-search" data-yw-search-open aria-label="検索"></button>
</header>

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

  <div class="yw-sp-search-shortcuts">
      <a href="{{ route('articles.index', ['sort' => 'latest']) }}">新着記事</a>
      <a href="{{ route('articles.index', ['sort' => 'category']) }}">カテゴリー</a>
  </div>
</div>
