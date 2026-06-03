{{-- SP版：resources/views/messages/index_sp.blade.php --}}
<div class="block md:hidden min-h-screen w-full overflow-x-hidden bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto min-h-screen w-full max-w-[430px] overflow-x-hidden bg-[#F8FAFF] px-3 pb-24 pt-6">

      @if (session('success'))
          <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[14px] font-bold text-emerald-700">
              {{ session('success') }}
          </div>
      @endif

      {{-- タイトル --}}
      <section class="mb-7">
          <h1 class="text-[34px] font-black leading-tight tracking-[0.01em] text-[#071433]">
              メッセージ一覧
          </h1>

          <p class="mt-3 text-[18px] font-bold leading-relaxed text-[#46516B]">
              気になるユーザーとの会話を確認できます
          </p>
      </section>

      {{-- 件数・検索 --}}
      <section class="mb-5">
          <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                  <p class="text-[23px] font-black text-[#071433]">
                      会話
                  </p>

                  <p class="text-[24px] font-black text-[#0D4FE8]">
                      {{ $conversationCount }}件
                  </p>

                  @if ($totalUnreadCount > 0)
                      <span class="inline-flex h-[44px] items-center gap-2 rounded-full border border-[#DDE6F5] bg-white px-4 text-[17px] font-black text-[#071433] shadow-[0_8px_18px_rgba(15,43,95,0.06)]">
                          <span class="h-2.5 w-2.5 rounded-full bg-[#0D4FE8]"></span>
                          未読あり
                      </span>
                  @endif
              </div>

              <button
                  type="button"
                  class="flex h-[54px] w-[128px] items-center justify-center gap-2 rounded-[14px] border border-[#DDE6F5] bg-white text-[19px] font-bold text-[#64748B] shadow-[0_8px_18px_rgba(15,43,95,0.06)]">
                  <span class="text-[27px]">⌕</span>
                  検索
              </button>
          </div>
      </section>

      {{-- メッセージ一覧 --}}
      <section>
          <div class="space-y-3">
              @forelse ($messageItems as $item)
                  @php
                      $partner = $item['partner'];
                      $unreadCount = $item['unread_count'];
                  @endphp

                  <a href="{{ route('messages.users.show', $partner) }}"
                      class="relative block overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-4 py-4 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">

                      @if ($unreadCount > 0)
                          <span class="absolute left-4 top-5 h-3 w-3 rounded-full bg-[#0D4FE8]"></span>
                      @endif

                      <div class="flex items-center gap-4">
                          <div class="h-[82px] w-[82px] shrink-0 overflow-hidden rounded-full bg-blue-50">
                              <img
                                  src="{{ $item['avatar_url'] }}"
                                  alt="{{ $item['display_name'] }}のプロフィール画像"
                                  class="h-full w-full object-cover"
                              >
                          </div>

                          <div class="min-w-0 flex-1">
                              <div class="flex items-start justify-between gap-3">
                                  <div class="min-w-0">
                                      <h2 class="truncate text-[27px] font-black leading-tight text-[#071433]">
                                          {{ $item['display_name'] }}
                                      </h2>

                                      <p class="mt-1 truncate text-[19px] font-bold leading-tight text-[#0D4FE8]">
                                          {{ $item['job_type'] }}
                                      </p>
                                  </div>

                                  <p class="shrink-0 text-[18px] font-bold text-[#64748B]">
                                      {{ $item['sp_time'] }}
                                  </p>
                              </div>

                              <div class="mt-3 flex items-center gap-3">
                                  <p class="min-w-0 flex-1 truncate text-[17px] font-bold leading-relaxed text-[#071433]">
                                      @if ($item['is_mine'])
                                          あなた：
                                      @else
                                          {{ $item['display_name'] }}：
                                      @endif
                                      {{ $item['last_body'] }}
                                  </p>

                                  @if ($unreadCount > 0)
                                      <span class="flex h-[38px] min-w-[78px] shrink-0 items-center justify-center rounded-[12px] bg-[#0D4FE8] px-3 text-[18px] font-black text-white">
                                          未読 {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                      </span>
                                  @endif

                                  <span class="shrink-0 text-[34px] leading-none text-[#8793A8]">
                                      ›
                                  </span>
                              </div>
                          </div>
                      </div>
                  </a>
              @empty
                  <div class="rounded-[18px] border border-dashed border-[#CBD7EA] bg-white px-5 py-10 text-center shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                      <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-[34px]">
                          💬
                      </div>

                      <h2 class="mt-4 text-[22px] font-black text-[#071433]">
                          まだメッセージはありません
                      </h2>

                      <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#64748B]">
                          気になるユーザーにメッセージを送ってみましょう。
                      </p>

                      <a href="{{ route('trainings.ranking') }}"
                          class="mt-6 inline-flex h-[48px] items-center justify-center rounded-[12px] bg-[#0D4FE8] px-6 text-[16px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.24)]">
                          ランキングを見る
                      </a>
                  </div>
              @endforelse
          </div>
      </section>
  </div>
</div>
