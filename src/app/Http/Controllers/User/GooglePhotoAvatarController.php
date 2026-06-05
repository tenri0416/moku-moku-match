<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Support\ApiActionLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class GooglePhotoAvatarController extends Controller
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const PHOTOS_PICKER_API = 'https://photospicker.googleapis.com/v1';

    public function redirect(Request $request): RedirectResponse
    {
        if (! $this->hasConfig()) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Googleフォト連携の設定が未完了です。管理者にお問い合わせください。');
        }

        $state = Str::random(40);

        $request->session()->put('google_photos_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google_photos.client_id'),
            'redirect_uri' => config('services.google_photos.redirect'),
            'response_type' => 'code',
            'scope' => config('services.google_photos.scope'),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);

        ApiActionLogger::info(
            'GooglePhotoAvatarController::redirect',
            'GoogleフォトOAuth認証を開始',
            [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]
        );

        return redirect(self::AUTH_URL . '?' . $query);
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Googleフォト連携がキャンセルされました。');
        }

        $request->validate([
            'code' => ['required', 'string'],
            'state' => ['required', 'string'],
        ]);

        $sessionState = $request->session()->pull('google_photos_oauth_state');

        if (! $sessionState || ! hash_equals($sessionState, (string) $request->state)) {
            return redirect()
                ->route('profile.edit')
                ->with('error', 'Googleフォト連携の認証情報が正しくありません。もう一度お試しください。');
        }

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->post(self::TOKEN_URL, [
                    'client_id' => config('services.google_photos.client_id'),
                    'client_secret' => config('services.google_photos.client_secret'),
                    'redirect_uri' => config('services.google_photos.redirect'),
                    'grant_type' => 'authorization_code',
                    'code' => $request->code,
                ]);

            if (! $response->successful()) {
                Log::warning('GoogleフォトOAuthトークン取得に失敗しました。', [
                    'user_id' => Auth::id(),
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return redirect()
                    ->route('profile.edit')
                    ->with('error', 'Googleフォト連携に失敗しました。もう一度お試しください。');
            }

            $token = $response->json();

            $accessToken = $token['access_token'] ?? null;
            $expiresIn = (int) ($token['expires_in'] ?? 3600);

            if (! $accessToken) {
                return redirect()
                    ->route('profile.edit')
                    ->with('error', 'Googleフォト連携に失敗しました。アクセストークンを取得できませんでした。');
            }

            $request->session()->put('google_photos_access_token', $accessToken);
            $request->session()->put('google_photos_access_token_expires_at', now()->addSeconds($expiresIn - 60)->timestamp);

            ApiActionLogger::info(
                'GooglePhotoAvatarController::callback',
                'GoogleフォトOAuth認証に成功',
                [
                    'user_id' => Auth::id(),
                    'ip' => $request->ip(),
                ]
            );

            return redirect()->route('profile.avatar.google.select');
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('profile.edit')
                ->with('error', 'Googleフォト連携中にエラーが発生しました。時間をおいて再度お試しください。');
        }
    }

    public function select(Request $request): View|RedirectResponse
    {
        if (! $this->hasAccessToken($request)) {
            return redirect()->route('profile.avatar.google.redirect');
        }

        return view('profile.avatar-google-select');
    }

    public function createPickerSession(Request $request)
    {
        if (! $this->hasAccessToken($request)) {
            return response()->json([
                'message' => 'Googleフォトの認証期限が切れました。もう一度連携してください。',
                'redirect_url' => route('profile.avatar.google.redirect'),
            ], 401);
        }

        $accessToken = $request->session()->get('google_photos_access_token');

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(20)
                ->post(self::PHOTOS_PICKER_API . '/sessions', new \stdClass());

            if (! $response->successful()) {
                Log::warning('Google Photos Picker セッション作成に失敗しました。', [
                    'user_id' => Auth::id(),
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'message' => 'Googleフォト選択画面の作成に失敗しました。',
                ], 500);
            }

            $session = $response->json();

            $sessionId = $session['id'] ?? null;
            $pickerUri = $session['pickerUri'] ?? null;

            if (! $sessionId || ! $pickerUri) {
                return response()->json([
                    'message' => 'Googleフォト選択セッションの情報を取得できませんでした。',
                ], 500);
            }

            $request->session()->put('google_photos_picker_session_id', $sessionId);

            ApiActionLogger::info(
                'GooglePhotoAvatarController::createPickerSession',
                'Google Photos Picker セッション作成成功',
                [
                    'user_id' => Auth::id(),
                    'session_id' => $sessionId,
                ]
            );

            return response()->json([
                'session_id' => $sessionId,
                'picker_uri' => $pickerUri . '/autoclose',
                'polling_config' => $session['pollingConfig'] ?? null,
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Googleフォト選択画面の作成中にエラーが発生しました。',
            ], 500);
        }
    }

    public function showPickerSession(Request $request, string $sessionId)
    {
        if (! $this->hasAccessToken($request)) {
            return response()->json([
                'message' => 'Googleフォトの認証期限が切れました。もう一度連携してください。',
                'redirect_url' => route('profile.avatar.google.redirect'),
            ], 401);
        }

        if ($request->session()->get('google_photos_picker_session_id') !== $sessionId) {
            return response()->json([
                'message' => 'Googleフォト選択セッションが正しくありません。',
            ], 403);
        }

        $accessToken = $request->session()->get('google_photos_access_token');

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(20)
                ->get(self::PHOTOS_PICKER_API . '/sessions/' . urlencode($sessionId));

            if (! $response->successful()) {
                Log::warning('Google Photos Picker セッション取得に失敗しました。', [
                    'user_id' => Auth::id(),
                    'session_id' => $sessionId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'message' => 'Googleフォト選択状態を確認できませんでした。',
                ], 500);
            }

            return response()->json($response->json());
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Googleフォト選択状態の確認中にエラーが発生しました。',
            ], 500);
        }
    }

    public function saveSelectedPhoto(Request $request)
    {
        $request->validate([
            'session_id' => ['required', 'string'],
        ]);

        if (! $this->hasAccessToken($request)) {
            return response()->json([
                'message' => 'Googleフォトの認証期限が切れました。もう一度連携してください。',
                'redirect_url' => route('profile.avatar.google.redirect'),
            ], 401);
        }

        $sessionId = (string) $request->session_id;

        if ($request->session()->get('google_photos_picker_session_id') !== $sessionId) {
            return response()->json([
                'message' => 'Googleフォト選択セッションが正しくありません。',
            ], 403);
        }

        $accessToken = $request->session()->get('google_photos_access_token');

        try {
            $mediaResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(20)
                ->get(self::PHOTOS_PICKER_API . '/mediaItems', [
                    'sessionId' => $sessionId,
                    'pageSize' => 1,
                ]);

            if (! $mediaResponse->successful()) {
                Log::warning('Google Photos Picker 選択メディア取得に失敗しました。', [
                    'user_id' => Auth::id(),
                    'session_id' => $sessionId,
                    'status' => $mediaResponse->status(),
                    'body' => $mediaResponse->body(),
                ]);

                return response()->json([
                    'message' => '選択されたGoogleフォト画像を取得できませんでした。',
                ], 500);
            }

            $mediaItems = $mediaResponse->json('mediaItems', []);

            if (empty($mediaItems)) {
                return response()->json([
                    'message' => '画像が選択されていません。',
                ], 422);
            }

            $mediaItem = $mediaItems[0];

            $mimeType = $mediaItem['mediaFile']['mimeType']
                ?? $mediaItem['mimeType']
                ?? '';

            if (! Str::startsWith($mimeType, 'image/')) {
                return response()->json([
                    'message' => 'プロフィール画像には写真を選択してください。動画は使用できません。',
                ], 422);
            }

            $baseUrl = $mediaItem['mediaFile']['baseUrl']
                ?? $mediaItem['baseUrl']
                ?? null;

            if (! $baseUrl) {
                return response()->json([
                    'message' => '画像URLを取得できませんでした。',
                ], 500);
            }

            /*
             * Google Photos の baseUrl は、そのままではなくサイズ指定が必要です。
             * 512x512にトリミングしてプロフィール画像として保存します。
             */
            $downloadUrl = $baseUrl . '=w512-h512-c';

            $imageResponse = Http::withToken($accessToken)
                ->timeout(30)
                ->get($downloadUrl);

            if (! $imageResponse->successful()) {
                Log::warning('Googleフォト画像ダウンロードに失敗しました。', [
                    'user_id' => Auth::id(),
                    'session_id' => $sessionId,
                    'status' => $imageResponse->status(),
                    'body' => $imageResponse->body(),
                ]);

                return response()->json([
                    'message' => 'Googleフォト画像の保存に失敗しました。',
                ], 500);
            }

            $contentType = $imageResponse->header('Content-Type') ?: $mimeType;
            $extension = $this->extensionFromMimeType($contentType);

            $path = 'avatars/' . Auth::id() . '/google-photo-' . now()->format('YmdHis') . '-' . Str::random(8) . '.' . $extension;

            Storage::disk('public')->put($path, $imageResponse->body());

            $user = Auth::user();

            $profile = Profile::query()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'display_name' => $user->name,
                ]
            );

            if ($profile->avatar_path && Storage::disk('public')->exists($profile->avatar_path)) {
                Storage::disk('public')->delete($profile->avatar_path);
            }

            $profile->forceFill([
                'avatar_path' => $path,
            ])->save();

            $this->deletePickerSession($accessToken, $sessionId);

            $request->session()->forget([
                'google_photos_picker_session_id',
                'google_photos_access_token',
                'google_photos_access_token_expires_at',
            ]);

            ApiActionLogger::info(
                'GooglePhotoAvatarController::saveSelectedPhoto',
                'Googleフォト画像をプロフィール画像として保存',
                [
                    'user_id' => Auth::id(),
                    'path' => $path,
                ]
            );

            return response()->json([
                'message' => 'Googleフォトの画像をプロフィール画像に設定しました。',
                'redirect_url' => route('profile.edit'),
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Googleフォト画像の保存中にエラーが発生しました。',
            ], 500);
        }
    }

    private function deletePickerSession(string $accessToken, string $sessionId): void
    {
        try {
            Http::withToken($accessToken)
                ->timeout(10)
                ->delete(self::PHOTOS_PICKER_API . '/sessions/' . urlencode($sessionId));
        } catch (Throwable $e) {
            Log::warning('Google Photos Picker セッション削除に失敗しました。', [
                'session_id' => $sessionId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function hasConfig(): bool
    {
        return filled(config('services.google_photos.client_id'))
            && filled(config('services.google_photos.client_secret'))
            && filled(config('services.google_photos.redirect'));
    }

    private function hasAccessToken(Request $request): bool
    {
        $token = $request->session()->get('google_photos_access_token');
        $expiresAt = (int) $request->session()->get('google_photos_access_token_expires_at');

        return filled($token) && $expiresAt > now()->timestamp;
    }

    private function extensionFromMimeType(string $mimeType): string
    {
        return match (strtolower($mimeType)) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }
}
