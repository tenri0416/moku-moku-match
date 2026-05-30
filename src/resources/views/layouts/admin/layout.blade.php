<div class="min-h-screen lg:flex">
  @include('layouts.admin.sidebar')

  <div class="min-w-0 flex-1">
      @include('layouts.admin.mobile-header')
      @include('layouts.admin.desktop-header')
      @include('layouts.admin.content')
  </div>
</div>
@include('layouts.admin.notifications.modal')
@include('layouts.admin.notifications.script')
