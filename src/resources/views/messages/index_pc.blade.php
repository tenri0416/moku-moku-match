{{-- PC版：resources/views/messages/index_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1340px] px-8 py-10">

      @if (session('success'))
          <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[15px] font-bold text-emerald-700">
              {{ session('success') }}
          </div>
      @endif

      <div class="grid grid-cols-[1fr_360px] gap-8">
          {{-- 左側 --}}
          <main>
              {{-- タイトル --}}
              <section class="mb-8">
                  <p class="text-[14px] font-black tracking-[0.2em] text-[#0D4FE8]">
                      MESSAGES
                  </p>

                  <h1 class="mt-4 text-[42px] font-black leading-tight tracking-[0.01em] text-[#071433]">
                      メッセージ一覧
                  </h1>

                  <p class="mt-4 text-[17px] font-bold leading-relaxed text-[#46516B]">
                      ユーザー同士のメッセージ履歴を確認できます。
                  </p>
              </section>

              {{-- 会話一覧 --}}
              <section class="overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-5 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="mb-5 flex items-center justify-between">
                      <h2 class="text-[21px] font-black text-[#071433]">
                          会話一覧
                      </h2>

                      <p class="text-[17px] font-bold text-[#071433]">
                        <span data-message-index-conversation-count>{{ $conversationCount }}</span>件
                      </p>
                  </div>

                  <div class="space-y-3" data-message-index-list="pc">
                      @forelse ($messageItems as $item)
                          @php
                              $partner = $item['partner'];
                              $unreadCount = $item['unread_count'];
                          @endphp

                          <a href="{{ route('messages.users.show', $partner) }}"
                              class="block rounded-[16px] border border-[#DDE6F5] bg-white px-5 py-4 transition hover:border-[#8DB3FF] hover:bg-[#FBFCFF] hover:shadow-[0_10px_22px_rgba(15,43,95,0.08)]">
                              <div class="flex items-center gap-5">
                                  <div class="h-[76px] w-[76px] shrink-0 overflow-hidden rounded-full bg-blue-50">
                                      <img
                                          src="{{ $item['avatar_url'] }}"
                                          alt="{{ $item['display_name'] }}のプロフィール画像"
                                          class="h-full w-full object-cover"
                                      >
                                  </div>

                                  <div class="min-w-0 flex-1">
                                      <div class="flex items-start justify-between gap-4">
                                          <div class="min-w-0">
                                              <h3 class="truncate text-[22px] font-black leading-tight text-[#071433]">
                                                  {{ $item['display_name'] }}
                                              </h3>

                                              <p class="mt-1 truncate text-[15px] font-bold text-[#46516B]">
                                                  {{ $item['job_type'] }}
                                              </p>
                                          </div>

                                          <p class="shrink-0 text-[15px] font-bold text-[#64748B]">
                                              {{ $item['pc_time'] }}
                                          </p>
                                      </div>

                                      <div class="mt-3 flex items-center gap-3">
                                          <p class="min-w-0 flex-1 truncate text-[15px] font-bold leading-relaxed text-[#46516B]">
                                              @if ($item['is_mine'])
                                                  <span class="font-black text-[#64748B]">あなた：</span>
                                              @else
                                                  <span class="font-black text-[#071433]">{{ $item['display_name'] }}：</span>
                                              @endif

                                              {{ $item['last_body'] }}
                                          </p>

                                          @if ($unreadCount > 0)
                                              <span class="flex h-[32px] min-w-[72px] shrink-0 items-center justify-center rounded-[10px] bg-[#0D4FE8] px-3 text-[14px] font-black text-white">
                                                  未読 {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                              </span>
                                          @endif

                                          <span class="shrink-0 text-[32px] leading-none text-[#8793A8]">
                                              ›
                                          </span>
                                      </div>
                                  </div>
                              </div>
                          </a>
                      @empty
                          <div class="rounded-[16px] border border-dashed border-[#CBD7EA] bg-[#FBFCFF] px-6 py-12 text-center">
                              <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white text-[34px] shadow-sm">
                                  💬
                              </div>

                              <h3 class="mt-4 text-[22px] font-black text-[#071433]">
                                  まだメッセージはありません
                              </h3>

                              <p class="mt-3 text-[15px] font-bold leading-relaxed text-[#64748B]">
                                  ランキングやプロフィール画面から、気になるユーザーにメッセージを送ってみましょう。
                              </p>

                              <a href="{{ route('trainings.ranking') }}"
                                  class="mt-6 inline-flex h-[48px] items-center justify-center rounded-[12px] bg-[#0D4FE8] px-6 text-[16px] font-black text-white shadow-[0_10px_18px_rgba(13,79,232,0.24)]">
                                  ランキングを見る
                              </a>
                          </div>
                      @endforelse
                  </div>
              </section>
          </main>

          {{-- 右側 --}}
          <aside class="space-y-6 pt-14">
              {{-- メッセージのコツ --}}
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-6 flex items-center gap-3 text-[20px] font-black text-[#071433]">
                      <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-[22px]">
                          💡
                      </span>
                      メッセージのコツ
                  </h2>

                  <div class="space-y-0">
                      <div class="flex gap-4 border-b border-dashed border-[#DDE6F5] py-4 first:pt-0">
                          <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#0D4FE8] text-[14px] font-black text-[#0D4FE8]">
                              ✓
                          </span>
                          <p class="text-[15px] font-bold leading-relaxed text-[#071433]">
                              あいさつから始めて、相手に安心感を与えましょう
                          </p>
                      </div>

                      <div class="flex gap-4 border-b border-dashed border-[#DDE6F5] py-4">
                          <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#0D4FE8] text-[14px] font-black text-[#0D4FE8]">
                              ✓
                          </span>
                          <p class="text-[15px] font-bold leading-relaxed text-[#071433]">
                              具体的な話題を選ぶと、会話が続きやすくなります
                          </p>
                      </div>

                      <div class="flex gap-4 border-b border-dashed border-[#DDE6F5] py-4">
                          <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#0D4FE8] text-[14px] font-black text-[#0D4FE8]">
                              ✓
                          </span>
                          <p class="text-[15px] font-bold leading-relaxed text-[#071433]">
                              相手の返信には丁寧に返すことを心がけましょう
                          </p>
                      </div>

                      <div class="flex gap-4 pt-4">
                          <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-[#0D4FE8] text-[14px] font-black text-[#0D4FE8]">
                              ✓
                          </span>
                          <p class="text-[15px] font-bold leading-relaxed text-[#071433]">
                              一緒に目標を共有すると、より良い関係が築けます
                          </p>
                      </div>
                  </div>
              </section>

              {{-- 今日の状況 --}}
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-5 text-[21px] font-black text-[#071433]">
                      今日の状況
                  </h2>

                  <div class="grid grid-cols-3 divide-x divide-[#DDE6F5]">
                      <div class="text-center">
                          <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-blue-50 text-[26px]">
                              ✉️
                          </div>
                          <p class="mt-3 text-[14px] font-black text-[#46516B]">未読</p>
                          <p class="mt-1 text-[28px] font-black leading-none text-[#0D4FE8]">
                            <span data-message-index-total-unread-count>{{ $totalUnreadCount }}</span><span class="text-[15px]">件</span>
                          </p>
                      </div>

                      <div class="text-center">
                          <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-blue-50 text-[26px]">
                              💬
                          </div>
                          <p class="mt-3 text-[14px] font-black text-[#46516B]">会話</p>
                          <p class="mt-1 text-[28px] font-black leading-none text-[#0D4FE8]"data-message-index-conversation-count>
                              {{ $conversationCount }}<span class="text-[15px]" data-message-index-conversation-count>件</span>
                          </p>
                      </div>

                      <div class="text-center">
                          <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-[26px]">
                              ↗️
                          </div>
                          <p class="mt-3 text-[14px] font-black text-[#46516B]">返信率</p>
                          <p class="mt-1 text-[28px] font-black leading-none text-emerald-600">
                              {{ $replyRate }}<span class="text-[15px]">%</span>
                          </p>
                      </div>
                  </div>
              </section>

              {{-- 応援カード --}}
              <section class="relative overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-7 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="relative z-10 max-w-[210px]">
                      <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                          つながりが、成長を加速させる
                      </p>

                      <p class="mt-5 text-[15px] font-bold leading-relaxed text-[#46516B]">
                          素敵な仲間と出会って、<br>
                          一緒にもくもくと<br>
                          頑張りましょう！
                      </p>
                  </div>

                  <div class="absolute bottom-0 right-5 text-[92px] leading-none">
                      🪴
                  </div>

                  <div class="absolute bottom-0 left-0 h-10 w-full rounded-t-[60%] bg-blue-50"></div>
              </section>
          </aside>
      </div>
  </div>
</div>
