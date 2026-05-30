<main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
  <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">
      <div class="min-w-0 bg-white">
          <div class="border-t-4 border-[#0B1548]">
              @yield('content')
          </div>
      </div>

      @include('layouts.article.sidebar')
  </div>
</main>
