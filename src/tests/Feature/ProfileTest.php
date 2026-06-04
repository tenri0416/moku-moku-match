<?php

namespace Tests\Feature;

use App\Models\Prefecture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function プロフィール編集画面へアクセスした時_正常に表示される(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->get('/profile/edit');

        // Assert
        $response->assertOk();
        $response->assertViewIs('profile.edit');
    }

    #[Test]
    public function プロフィール情報を更新した時_profilesテーブルに保存されマイページへ遷移する(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $prefecture = $this->都道府県を作成する();

        // Act
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'display_name' => 'テスト太郎',
                'job_type' => 'Laravelエンジニア',
                'prefecture_id' => $prefecture->id,
                'skills' => 'PHP, Laravel, AWS',
                'bio' => 'フルリモートで開発しています。',
                'purpose' => '黙々作業仲間を探したい',
                'work_style' => '平日夜に作業したい',
            ]);

        // Assert
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/mypage');

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => 'テスト太郎',
            'job_type' => 'Laravelエンジニア',
            'prefecture_id' => $prefecture->id,
            'skills' => 'PHP, Laravel, AWS',
            'bio' => 'フルリモートで開発しています。',
            'purpose' => '黙々作業仲間を探したい',
            'work_style' => '平日夜に作業したい',
        ]);
    }

    #[Test]
    public function プロフィール情報を再更新した時_既存プロフィールが更新され重複作成されない(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $prefecture = $this->都道府県を作成する();

        $user->profile()->create([
            'display_name' => '変更前',
            'job_type' => '変更前職種',
            'prefecture_id' => $prefecture->id,
            'skills' => '変更前スキル',
            'bio' => '変更前自己紹介',
            'purpose' => '変更前目的',
            'work_style' => '変更前スタイル',
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'display_name' => '変更後',
                'job_type' => 'バックエンドエンジニア',
                'prefecture_id' => $prefecture->id,
                'skills' => 'Laravel, Docker',
                'bio' => '変更後の自己紹介です。',
                'purpose' => '勉強仲間を探したい',
                'work_style' => '朝活したい',
            ]);

        // Assert
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/mypage');

        $this->assertSame(1, $user->profile()->count());

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'display_name' => '変更後',
            'job_type' => 'バックエンドエンジニア',
            'skills' => 'Laravel, Docker',
            'bio' => '変更後の自己紹介です。',
            'purpose' => '勉強仲間を探したい',
            'work_style' => '朝活したい',
        ]);
    }

    #[Test]
    public function プロフィール削除で正しいパスワードを送信した時_ユーザーを削除できる(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        // Assert
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    #[Test]
    public function プロフィール削除で誤ったパスワードを送信した時_ユーザーは削除されない(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        // Act
        $response = $this
            ->actingAs($user)
            ->from('/profile/edit')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        // Assert
        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile/edit');

        $this->assertNotNull($user->fresh());
    }

    private function 都道府県を作成する(): Prefecture
    {
        return Prefecture::create([
            'slug' => 'tokyo',
            'name' => '東京都',
            'multilingual_json' => null,
        ]);
    }
}
