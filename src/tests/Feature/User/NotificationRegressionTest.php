<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function 通知一覧で_自分の通知が表示用データに渡される(): void
    {
        // Arrange
        $notificationId = $this->通知を作成する($this->user, null, route('mypage'));
        $this->通知を作成する($this->otherUser, null, route('home'));

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('notifications.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('notifications.index');

        $ids = collect($response->viewData('notifications')->items())->pluck('id')->all();

        $this->assertContains($notificationId, $ids);
    }

    #[Test]
    public function 通知詳細で_自分の通知なら既読にしてdataのURLへリダイレクトされる(): void
    {
        // Arrange
        $notificationId = $this->通知を作成する($this->user, null, route('mypage'));

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('notifications.show', $notificationId));

        // Assert
        $response->assertRedirect(route('mypage'));

        $this->assertNotNull(
            DB::table('notifications')->where('id', $notificationId)->value('read_at')
        );
    }

    #[Test]
    public function 通知詳細で_URLがない場合_通知一覧へリダイレクトされる(): void
    {
        // Arrange
        $notificationId = $this->通知を作成する($this->user, null, null);

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('notifications.show', $notificationId));

        // Assert
        $response->assertRedirect(route('notifications.index'));
    }

    #[Test]
    public function 通知詳細で_他人の通知IDを指定した場合_404になる(): void
    {
        // Arrange
        $notificationId = $this->通知を作成する($this->otherUser, null, route('home'));

        // Act
        $response = $this
            ->actingAs($this->user)
            ->get(route('notifications.show', $notificationId));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function 通知一括既読で_自分の未読通知をすべて既読にできる(): void
    {
        // Arrange
        $this->通知を作成する($this->user, null, route('mypage'));
        $this->通知を作成する($this->user, null, route('mypage'));
        $this->通知を作成する($this->otherUser, null, route('home'));

        // Act
        $response = $this
            ->actingAs($this->user)
            ->post(route('notifications.read-all'));

        // Assert
        $response->assertRedirect(route('notifications.index'));
        $response->assertSessionHas('success', 'すべての通知を既読にしました。');

        $this->assertSame(0, $this->user->fresh()->unreadNotifications()->count());
        $this->assertSame(1, $this->otherUser->fresh()->unreadNotifications()->count());
    }

    #[Test]
    public function 未ログインで通知一覧へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('notifications.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    private function 通知を作成する(User $user, mixed $readAt, ?string $url): string
    {
        $id = (string) Str::uuid();

        DB::table('notifications')->insert([
            'id' => $id,
            'type' => 'Tests\\Notifications\\DummyUserNotification',
            'notifiable_type' => $user::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(array_filter([
                'message' => 'テスト通知',
                'url' => $url,
            ], fn ($value) => $value !== null), JSON_UNESCAPED_UNICODE),
            'read_at' => $readAt,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $id;
    }
}
