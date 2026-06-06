@if (session('article_inquiry_success'))
    <div class="yw-contact-flash-success" data-yw-contact-flash-success>
        {{ session('article_inquiry_success') }}
    </div>
@endif

@unless (session('article_inquiry_success'))
    <div
        class="yw-contact-overlay {{ $errors->has('email') || $errors->has('body') ? 'is-open' : '' }}"
        data-yw-contact-modal
        aria-hidden="{{ $errors->has('email') || $errors->has('body') ? 'false' : 'true' }}"
    >
        <div class="yw-contact-modal">
            <button type="button" class="yw-contact-close" data-yw-contact-close>
                ×
            </button>

            <p class="yw-contact-kicker">
                CONTACT
            </p>

            <h2 class="yw-contact-title">
                お問い合わせ
            </h2>

            <p class="yw-contact-description">
                YomuWorksへのお問い合わせはこちらから送信できます。
            </p>

            @if ($errors->has('email') || $errors->has('body'))
                <div class="yw-contact-error">
                    入力内容をご確認ください。
                </div>
            @endif

            <form
                action="{{ route('article-inquiries.store') }}"
                method="POST"
                class="yw-contact-form"
                data-yw-contact-form
            >
                @csrf

                <label>
                    <span>メールアドレス</span>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="example@example.com"
                        required
                    >
                </label>

                @error('email')
                    <p class="yw-form-error">{{ $message }}</p>
                @enderror

                <label>
                    <span>お問い合わせ内容</span>
                    <textarea
                        name="body"
                        rows="6"
                        placeholder="お問い合わせ内容を入力してください"
                        required
                    >{{ old('body') }}</textarea>
                </label>

                @error('body')
                    <p class="yw-form-error">{{ $message }}</p>
                @enderror

                <button type="submit" class="yw-contact-submit" data-yw-contact-submit>
                    送信する
                </button>
            </form>
        </div>
    </div>
@endunless

@once
    <style>
        .yw-contact-overlay {
            display: none;
        }

        .yw-contact-overlay.is-open {
            display: flex;
        }

        .yw-contact-flash-success {
            position: fixed;
            top: 88px;
            left: 50%;
            z-index: 9999;
            transform: translateX(-50%);
            width: min(92vw, 520px);
            border: 1px solid rgba(34, 197, 94, 0.28);
            border-radius: 18px;
            background: #ecfdf5;
            color: #047857;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            text-align: center;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.querySelector('[data-yw-contact-modal]');
            const form = document.querySelector('[data-yw-contact-form]');
            const closeButtons = document.querySelectorAll('[data-yw-contact-close]');
            const openButtons = document.querySelectorAll('[data-yw-contact-open]');
            const flashSuccess = document.querySelector('[data-yw-contact-flash-success]');

            const closeModal = () => {
                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            };

            const openModal = () => {
                if (!modal) {
                    return;
                }

                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            openButtons.forEach((button) => {
                button.addEventListener('click', openModal);
            });

            closeButtons.forEach((button) => {
                button.addEventListener('click', closeModal);
            });

            if (modal) {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal();
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            if (form) {
                form.addEventListener('submit', () => {
                    const submitButton = form.querySelector('[data-yw-contact-submit]');

                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.textContent = '送信中...';
                    }

                    closeModal();
                });
            }

            if (flashSuccess) {
                document.body.style.overflow = '';

                setTimeout(() => {
                    flashSuccess.style.opacity = '0';
                    flashSuccess.style.transition = 'opacity 0.4s ease';

                    setTimeout(() => {
                        flashSuccess.remove();
                    }, 400);
                }, 4000);
            }
        });
    </script>
@endonce
