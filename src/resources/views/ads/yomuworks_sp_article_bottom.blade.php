{{-- YomuWorks SP記事下広告 --}}
@if (app()->environment('production'))
    <div class="yw-ad-box yw-ad-box-sp">
        <!-- admax -->
        <script src="https://adm.shinobi.jp/s/2a347fb511401a00157d326d7920ea16"></script>
        <!-- admax -->
    </div>
@else
    <div class="yw-ad-box yw-ad-box-sp">
        <div class="yw-ad-dummy-sp">
            広告表示エリア 300×50
        </div>
    </div>
@endif
