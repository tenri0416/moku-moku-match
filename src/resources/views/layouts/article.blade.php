<!DOCTYPE html>
<html lang="ja">
@include('layouts.article.head')

<body class="yomuworks-body">
    <div class="yomuworks-pc">
        @include('layouts.article.pc-header')

        <main>
            @yield('pc_content')
        </main>

        @include('layouts.article.pc-footer')
    </div>

    <div class="yomuworks-sp">
        @include('layouts.article.sp-header')

        <main>
            @yield('sp_content')
        </main>

        @include('layouts.article.sp-footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchOpenButtons = document.querySelectorAll('[data-yw-search-open]');
            const searchCloseButtons = document.querySelectorAll('[data-yw-search-close]');
            const searchPanels = document.querySelectorAll('[data-yw-search-panel]');

            searchOpenButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    searchPanels.forEach(function (panel) {
                        panel.classList.add('is-open');

                        const input = panel.querySelector('input[name="keyword"]');
                        if (input) {
                            setTimeout(function () {
                                input.focus();
                            }, 50);
                        }
                    });
                });
            });

            searchCloseButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    searchPanels.forEach(function (panel) {
                        panel.classList.remove('is-open');
                    });
                });
            });

            const contactOpenButtons = document.querySelectorAll('[data-yw-contact-open]');
            const contactCloseButtons = document.querySelectorAll('[data-yw-contact-close]');
            const contactModals = document.querySelectorAll('[data-yw-contact-modal]');

            contactOpenButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    contactModals.forEach(function (modal) {
                        modal.classList.add('is-open');
                    });
                });
            });

            contactCloseButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    contactModals.forEach(function (modal) {
                        modal.classList.remove('is-open');
                    });
                });
            });

            contactModals.forEach(function (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.remove('is-open');
                    }
                });
            });

            @if (session('article_inquiry_success') || $errors->has('email') || $errors->has('body'))
                contactModals.forEach(function (modal) {
                    modal.classList.add('is-open');
                });
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
