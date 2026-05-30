<x-app-layout>
  <div class="max-w-4xl mx-auto py-10 px-4">
      <div class="flex justify-between items-center mb-6">
          <h1 class="text-2xl font-bold">通知</h1>

          <form method="POST" action="{{ route('notifications.read-all') }}">
              @csrf
              <button type="submit" class="text-sm text-blue-600 hover:underline">
                  すべて既読にする
              </button>
          </form>
      </div>

      @if (session('success'))
          <div class="bg-green-100 text-green-700 rounded-lg px-4 py-3 mb-4">
              {{ session('success') }}
          </div>
      @endif

      <div class="bg-white rounded-lg shadow divide-y">
          @forelse ($notifications as $notification)
              <a href="{{ route('notifications.show', $notification->id) }}"
                 class="block p-4 hover:bg-gray-50 {{ is_null($notification->read_at) ? 'bg-blue-50' : '' }}">
                  <div class="font-bold">
                      {{ $notification->data['title'] ?? '通知' }}
                  </div>

                  <div class="text-sm text-gray-600 mt-1">
                      {{ $notification->data['body'] ?? '' }}
                  </div>

                  <div class="text-xs text-gray-400 mt-2">
                      {{ $notification->created_at->diffForHumans() }}
                  </div>
              </a>
          @empty
              <div class="p-6 text-gray-600">
                  通知はありません。
              </div>
          @endforelse
      </div>

      <div class="mt-6">
          {{ $notifications->links() }}
      </div>
  </div>
</x-app-layout>
