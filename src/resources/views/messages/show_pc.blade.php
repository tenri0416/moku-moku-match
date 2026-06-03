{{-- PC版：resources/views/messages/show_pc.blade.php --}}
<div class="hidden md:block min-h-screen bg-[#F8FAFF] text-[#071433]">
  <div class="mx-auto w-full max-w-[1180px] px-8 py-10">

      <div class="grid grid-cols-[1fr_340px] gap-8">
          {{-- 左側 --}}
          <main>
              {{-- ヘッダー --}}
              <section class="mb-6 rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-5 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="flex items-center justify-between gap-6">
                      <div class="flex min-w-0 items-center gap-4">
                          <div class="h-[72px] w-[72px] shrink-0 overflow-hidden rounded-full bg-blue-50">
                              <img
                                  src="{{ $partnerAvatarUrl }}"
                                  alt="{{ $partnerDisplayName }}のプロフィール画像"
                                  class="h-full w-full object-cover"
                              >
                          </div>

                          <div class="min-w-0">
                              <p class="text-[13px] font-black tracking-[0.2em] text-[#0D4FE8]">
                                  MESSAGE
                              </p>

                              <h1 class="mt-2 truncate text-[30px] font-black text-[#071433]">
                                  {{ $partnerDisplayName }} さんとのメッセージ
                              </h1>

                              <p class="mt-1 truncate text-[15px] font-bold text-[#46516B]">
                                  {{ $partnerJobType }} / {{ $workPostTitle }}
                              </p>
                          </div>
                      </div>

                      <div class="flex shrink-0 gap-3">
                          <a href="{{ route('work-posts.show', $workPost) }}"
                              class="flex h-[44px] items-center justify-center rounded-[12px] border border-[#CBD7EA] bg-white px-5 text-[14px] font-black text-[#071433]">
                              募集詳細
                          </a>

                          <a href="{{ route('messages.index') }}"
                              class="flex h-[44px] items-center justify-center rounded-[12px] bg-[#0D4FE8] px-5 text-[14px] font-black text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)]">
                              一覧へ戻る
                          </a>
                      </div>
                  </div>
              </section>

              {{-- メッセージエリア --}}
              <section class="overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="h-[560px] space-y-5 overflow-y-auto px-6 py-6" data-message-list>
                      @forelse ($messages as $message)
                          @php
                              $isMine = (int) $message->sender_id === (int) auth()->id();

                              $sender = $message->sender;
                              $senderProfile = $sender?->profile;
                              $senderName = $senderProfile?->display_name ?? $sender?->name ?? 'ユーザー';

                              $senderAvatarPath = $senderProfile?->avatar_path;
                              $senderAvatarUrl = $senderAvatarPath
                                  ? asset('storage/' . ltrim($senderAvatarPath, '/'))
                                  : asset('images/default-avatar.png');
                          @endphp

                          <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $message->id }}">
                              <div class="flex max-w-[72%] gap-3 {{ $isMine ? 'flex-row-reverse' : '' }}">
                                  @unless ($isMine)
                                      <div class="h-11 w-11 shrink-0 overflow-hidden rounded-full bg-blue-50">
                                          <img
                                              src="{{ $senderAvatarUrl }}"
                                              alt="{{ $senderName }}のプロフィール画像"
                                              class="h-full w-full object-cover"
                                          >
                                      </div>
                                  @endunless

                                  <div>
                                      <div class="mb-1 flex items-center gap-2 {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                          <span class="text-[12px] font-bold text-[#64748B]">
                                              {{ $isMine ? 'あなた' : $senderName }}
                                          </span>

                                          <span class="text-[12px] font-bold text-[#94A3B8]">
                                              {{ $message->created_at->format('Y/m/d H:i') }}
                                          </span>
                                      </div>

                                      <div class="{{ $isMine ? 'rounded-br-[6px] bg-[#0D4FE8] text-white' : 'rounded-bl-[6px] bg-[#F1F5F9] text-[#071433]' }} rounded-[18px] px-5 py-3 text-[15px] font-bold leading-7 shadow-sm">
                                          {!! nl2br(e($message->body)) !!}
                                      </div>

                                      @if ($isMine)
                                          <div class="mt-1 text-right text-[12px] font-bold text-[#94A3B8]">
                                              {{ $message->read_at ? '既読' : '未読' }}
                                          </div>
                                      @endif
                                  </div>
                              </div>
                          </div>
                      @empty
                          <div class="flex h-full items-center justify-center text-center" data-empty-message>
                              <div>
                                  <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-[34px]">
                                      💬
                                  </div>

                                  <h2 class="mt-4 text-[22px] font-black text-[#071433]">
                                      まだメッセージはありません
                                  </h2>

                                  <p class="mt-2 text-[15px] font-bold leading-relaxed text-[#64748B]">
                                      最初のメッセージを送信して、やり取りを始めましょう。
                                  </p>
                              </div>
                          </div>
                      @endforelse
                  </div>

                  {{-- 送信フォーム --}}
                  <div class="border-t border-[#DDE6F5] bg-[#FBFCFF] px-6 py-5">
                      <form
                          method="POST"
                          action="{{ route('messages.store', [$workPost, $user]) }}"
                          data-message-form
                      >
                          @csrf

                          <label for="body-pc" class="mb-2 block text-[15px] font-black text-[#071433]">
                              メッセージ本文
                          </label>

                          <textarea
                              id="body-pc"
                              name="body"
                              rows="4"
                              required
                              maxlength="2000"
                              placeholder="メッセージを入力してください"
                              class="block w-full resize-none rounded-[14px] border border-[#CBD7EA] bg-white px-4 py-3 text-[15px] font-bold leading-7 text-[#071433] outline-none placeholder:text-[#94A3B8] focus:border-[#0D4FE8] focus:ring-4 focus:ring-blue-100"
                              data-message-body
                          >{{ old('body') }}</textarea>

                          @error('body')
                              <p class="mt-2 rounded-xl bg-red-50 px-3 py-2 text-[13px] font-bold text-red-600">
                                  {{ $message }}
                              </p>
                          @enderror

                          <div class="mt-4 flex items-center justify-end gap-3">
                              <a href="{{ route('messages.index') }}"
                                  class="flex h-[48px] items-center justify-center rounded-[12px] border border-[#CBD7EA] bg-white px-6 text-[15px] font-black text-[#071433]">
                                  メッセージ一覧へ戻る
                              </a>

                              <button
                                  type="submit"
                                  class="flex h-[48px] items-center justify-center rounded-[12px] bg-[#0D4FE8] px-8 text-[15px] font-black text-white shadow-[0_8px_16px_rgba(13,79,232,0.22)] disabled:cursor-not-allowed disabled:opacity-60"
                                  data-message-submit
                              >
                                  送信する
                              </button>
                          </div>
                      </form>
                  </div>
              </section>
          </main>

          {{-- 右側 --}}
          <aside class="space-y-6">
              <section class="rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-6 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <h2 class="mb-5 flex items-center gap-3 text-[20px] font-black text-[#071433]">
                      💡 メッセージのコツ
                  </h2>

                  <ul class="space-y-4 text-[15px] font-bold leading-relaxed text-[#46516B]">
                      <li class="flex gap-3">
                          <span class="text-[#0D4FE8]">✓</span>
                          返信しやすいように、要件を短くまとめましょう
                      </li>
                      <li class="flex gap-3">
                          <span class="text-[#0D4FE8]">✓</span>
                          相手の状況に配慮して、丁寧に伝えましょう
                      </li>
                      <li class="flex gap-3">
                          <span class="text-[#0D4FE8]">✓</span>
                          一緒に作業する時間や目的を具体的に書きましょう
                      </li>
                  </ul>
              </section>

              <section class="relative overflow-hidden rounded-[18px] border border-[#DDE6F5] bg-white px-6 py-7 shadow-[0_8px_22px_rgba(15,43,95,0.06)]">
                  <div class="relative z-10 max-w-[220px]">
                      <p class="text-[18px] font-black leading-relaxed text-[#0D4FE8]">
                          ひとことの返信が、次のつながりになります。
                      </p>

                      <p class="mt-4 text-[15px] font-bold leading-relaxed text-[#46516B]">
                          無理なく、あなたのペースで<br>
                          やり取りしていきましょう。
                      </p>
                  </div>

                  <div class="absolute bottom-0 right-4 text-[84px] leading-none">
                      🪴
                  </div>
              </section>
          </aside>
      </div>
  </div>
</div>
