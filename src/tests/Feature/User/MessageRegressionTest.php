<?php

namespace Tests\Feature\User;

use App\Models\Message;
use App\Models\User;
use App\Notifications\MessageReceivedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageRegressionTest extends TestCase
{
    use RefreshDatabase;

    private User $loginUser;

    private User $partnerUser;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->loginUser = User::factory()->create([
            'name' => 'ログインユーザー',
            'email_verified_at' => now(),
        ]);

        $this->partnerUser = User::factory()->create([
            'name' => '相手ユーザー',
            'email_verified_at' => now(),
        ]);
    }

    #[Test]
    public function メッセージ一覧で_自分に関係する会話だけが会話相手ごとに表示される(): void
    {
        // Arrange
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $this->partnerUser,
            body: '相手ユーザーへの古いメッセージ',
            createdAt: now()->subMinutes(10),
        );

        $latestMessage = $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '相手ユーザーからの最新メッセージ',
            createdAt: now()->subMinutes(5),
        );

        $this->メッセージを作成する(
            sender: $otherUser,
            receiver: $this->partnerUser,
            body: 'ログインユーザーに関係ないメッセージ',
            createdAt: now(),
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('messages.index'));

        // Assert
        $response->assertOk();
        $response->assertViewIs('messages.index');

        $messageItems = $response->viewData('messageItems');

        $this->assertCount(1, $messageItems);
        $this->assertSame($latestMessage->id, $messageItems->first()['message']->id);
        $this->assertSame($this->partnerUser->id, $messageItems->first()['partner']->id);
        $this->assertSame('相手ユーザーからの最新メッセージ', $messageItems->first()['message']->body);
    }

    #[Test]
    public function メッセージ一覧で_相手からの未読メッセージ数が表示用データに含まれる(): void
    {
        // Arrange
        $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '未読1',
            readAt: null,
        );

        $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '未読2',
            readAt: null,
        );

        $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $this->partnerUser,
            body: '自分から送ったメッセージ',
            readAt: null,
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('messages.index'));

        // Assert
        $response->assertOk();

        $messageItems = $response->viewData('messageItems');

        $this->assertSame(1, $response->viewData('conversationCount'));
        $this->assertSame(2, $response->viewData('totalUnreadCount'));
        $this->assertSame(2, $messageItems->first()['unread_count']);
    }

    #[Test]
    public function メッセージ一覧で_自分が送った会話に返信がある場合_返信率が算出される(): void
    {
        // Arrange
        $repliedPartner = $this->partnerUser;

        $notRepliedPartner = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $repliedPartner,
            body: '返信あり会話への送信',
        );

        $this->メッセージを作成する(
            sender: $repliedPartner,
            receiver: $this->loginUser,
            body: '返信あり会話からの返信',
        );

        $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $notRepliedPartner,
            body: '返信なし会話への送信',
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('messages.index'));

        // Assert
        $response->assertOk();
        $this->assertSame(50, $response->viewData('replyRate'));
    }

    #[Test]
    public function メッセージ詳細で_自分と相手の会話だけが表示され相手からの未読が既読になる(): void
    {
        // Arrange
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $unreadMessage = $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '既読になるメッセージ',
            readAt: null,
        );

        $ownMessage = $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $this->partnerUser,
            body: '自分が送ったメッセージ',
            readAt: null,
        );

        $this->メッセージを作成する(
            sender: $otherUser,
            receiver: $this->loginUser,
            body: '別ユーザーからのメッセージ',
            readAt: null,
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('messages.users.show', $this->partnerUser));

        // Assert
        $response->assertOk();
        $response->assertViewIs('messages.user-show');

        $messages = $response->viewData('messages');

        $this->assertCount(2, $messages);
        $this->assertTrue($messages->pluck('id')->contains($unreadMessage->id));
        $this->assertTrue($messages->pluck('id')->contains($ownMessage->id));

        $this->assertNotNull($unreadMessage->fresh()->read_at);
        $this->assertNull($ownMessage->fresh()->read_at);
    }

    #[Test]
    public function メッセージ詳細で_自分自身を指定した場合_404になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->get(route('messages.users.show', $this->loginUser));

        // Assert
        $response->assertNotFound();
    }

    #[Test]
    public function メッセージ送信で_正しい本文ならDBに保存され相手のDM画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->post(route('messages.users.store', $this->partnerUser), [
                'body' => 'こんにちは。黙々作業をご一緒したいです。',
            ]);

        // Assert
        $response->assertRedirect(route('messages.users.show', $this->partnerUser));
        $response->assertSessionHas('success', 'メッセージを送信しました。');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->loginUser->id,
            'receiver_id' => $this->partnerUser->id,
            'body' => 'こんにちは。黙々作業をご一緒したいです。',
        ]);

        Notification::assertSentTo(
            $this->partnerUser,
            MessageReceivedNotification::class
        );
    }

    #[Test]
    public function メッセージ送信で_JSONリクエストの場合_JSONで作成メッセージが返る(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->postJson(route('messages.users.store', $this->partnerUser), [
                'body' => 'JSONで送信するメッセージです。',
            ]);

        // Assert
        $response->assertOk();
        $response->assertJsonPath('message.body', 'JSONで送信するメッセージです。');
        $response->assertJsonPath('message.sender_id', $this->loginUser->id);
        $response->assertJsonPath('message.sender_name', $this->loginUser->name);
        $response->assertJsonPath('message.is_mine', true);
        $response->assertJsonPath('message.read_label', '未読');

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->loginUser->id,
            'receiver_id' => $this->partnerUser->id,
            'body' => 'JSONで送信するメッセージです。',
        ]);
    }

    #[Test]
    public function メッセージ送信で_自分自身を指定した場合_403になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->post(route('messages.users.store', $this->loginUser), [
                'body' => '自分自身には送れないメッセージです。',
            ]);

        // Assert
        $response->assertForbidden();

        $this->assertDatabaseMissing('messages', [
            'sender_id' => $this->loginUser->id,
            'receiver_id' => $this->loginUser->id,
            'body' => '自分自身には送れないメッセージです。',
        ]);
    }

    #[Test]
    #[DataProvider('不正なメッセージ本文一覧')]
    public function メッセージ送信で_本文が不正な場合_バリデーションエラーになる(array $payload): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->from(route('messages.users.show', $this->partnerUser))
            ->post(route('messages.users.store', $this->partnerUser), $payload);

        // Assert
        $response->assertRedirect(route('messages.users.show', $this->partnerUser));
        $response->assertSessionHasErrors(['body']);

        $this->assertSame(0, Message::count());
    }

    public static function 不正なメッセージ本文一覧(): array
    {
        return [
            '本文が空' => [
                [
                    'body' => '',
                ],
            ],
            '本文が2001文字' => [
                [
                    'body' => str_repeat('あ', 2001),
                ],
            ],
        ];
    }

    #[Test]
    public function メッセージ送信で_本文が2000文字なら保存できる(): void
    {
        // Arrange
        $body = str_repeat('あ', 2000);

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->post(route('messages.users.store', $this->partnerUser), [
                'body' => $body,
            ]);

        // Assert
        $response->assertRedirect(route('messages.users.show', $this->partnerUser));

        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->loginUser->id,
            'receiver_id' => $this->partnerUser->id,
            'body' => $body,
        ]);
    }

    #[Test]
    public function 新着メッセージ取得で_after_idより大きい自分と相手の会話だけJSONで返る(): void
    {
        // Arrange
        $otherUser = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $oldMessage = $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '古いメッセージ',
            readAt: now(),
        );

        $newMessage = $this->メッセージを作成する(
            sender: $this->partnerUser,
            receiver: $this->loginUser,
            body: '新しいメッセージ',
            readAt: null,
        );

        $this->メッセージを作成する(
            sender: $otherUser,
            receiver: $this->loginUser,
            body: '別ユーザーからの新着',
            readAt: null,
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->getJson(route('messages.users.latest', [
                'user' => $this->partnerUser,
                'after_id' => $oldMessage->id,
            ]));

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'messages');
        $response->assertJsonPath('messages.0.id', $newMessage->id);
        $response->assertJsonPath('messages.0.body', '新しいメッセージ');
        $response->assertJsonPath('messages.0.is_mine', false);

        $this->assertNotNull($newMessage->fresh()->read_at);
    }

    #[Test]
    public function 新着メッセージ取得で_自分が送ったメッセージはis_mineがtrueになる(): void
    {
        // Arrange
        $message = $this->メッセージを作成する(
            sender: $this->loginUser,
            receiver: $this->partnerUser,
            body: '自分が送った新着メッセージ',
            readAt: null,
        );

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->getJson(route('messages.users.latest', [
                'user' => $this->partnerUser,
                'after_id' => 0,
            ]));

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'messages');
        $response->assertJsonPath('messages.0.id', $message->id);
        $response->assertJsonPath('messages.0.is_mine', true);
        $response->assertJsonPath('messages.0.read_label', '未読');

        $this->assertNull($message->fresh()->read_at);
    }

    #[Test]
    public function 新着メッセージ取得で_自分自身を指定した場合_403になる(): void
    {
        // Arrange

        // Act
        $response = $this
            ->actingAs($this->loginUser)
            ->getJson(route('messages.users.latest', [
                'user' => $this->loginUser,
                'after_id' => 0,
            ]));

        // Assert
        $response->assertForbidden();
    }

    #[Test]
    public function 未ログインでメッセージ一覧へアクセスした場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->get(route('messages.index'));

        // Assert
        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function 未ログインでメッセージ送信した場合_ログイン画面へリダイレクトされる(): void
    {
        // Arrange

        // Act
        $response = $this->post(route('messages.users.store', $this->partnerUser), [
            'body' => '未ログイン送信',
        ]);

        // Assert
        $response->assertRedirect(route('login'));

        $this->assertDatabaseMissing('messages', [
            'receiver_id' => $this->partnerUser->id,
            'body' => '未ログイン送信',
        ]);
    }

    private function メッセージを作成する(
        User $sender,
        User $receiver,
        string $body,
        mixed $readAt = null,
        mixed $createdAt = null,
    ): Message {
        $message = Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => $body,
            'read_at' => $readAt,
        ]);

        if ($createdAt) {
            $message->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->save();
        }

        return $message->fresh();
    }
}
