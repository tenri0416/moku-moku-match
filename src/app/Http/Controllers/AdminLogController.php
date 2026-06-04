<?php

namespace App\Http\Controllers;

use App\Support\ApiActionLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminLogController extends Controller
{
    /**
     * ログファイル一覧
     */
    public function index(Request $request)
    {
        // デフォルトは error
        $type = $request->input('type', 'error');
        $date = $request->input('date');

        if (! $this->isValidLogType($type)) {
            $type = 'error';
        }

        ApiActionLogger::info(
            'AdminLogController::index',
            '管理者ログファイル一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'type' => $type,
                'date' => $date,
            ]
        );

        $logFiles = collect(File::files(storage_path('logs')))
            ->filter(function ($file) {
                return $this->isValidLogFileName($file->getFilename());
            })
            ->filter(function ($file) use ($type) {
                return $this->getTypeFromFileName($file->getFilename()) === $type;
            })
            ->when($date, function ($files) use ($date, $type) {
                return $files->filter(function ($file) use ($date, $type) {
                    return $file->getFilename() === $this->makeLogFileName($type, $date);
                });
            })
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) {
                $fileName = $file->getFilename();
                $type = $this->getTypeFromFileName($fileName);

                return [
                    'name' => $fileName,
                    'type' => $type,
                    'type_label' => $this->getTypeLabel($type),
                    'date' => $this->getDateFromFileName($fileName),
                    'size' => $this->formatBytes($file->getSize()),
                    'updated_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            })
            ->values();

        ApiActionLogger::info(
            'AdminLogController::index',
            '管理者ログファイル一覧取得完了',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'type' => $type,
                'date' => $date,
                'log_file_count' => $logFiles->count(),
            ]
        );

        return view('admin.logs.index', [
            'logFiles' => $logFiles,
            'type' => $type,
            'date' => $date,
            'logTypes' => $this->logTypes(),
        ]);
    }

    /**
     * ログファイル詳細
     */
    public function show(Request $request, string $file)
    {
        abort_unless($this->isValidLogFileName($file), 404);

        $path = storage_path('logs/' . $file);

        abort_unless(File::exists($path), 404);

        $type = $request->input('type', $this->getTypeFromFileName($file) ?? 'error');
        $date = $request->input('date', $this->getDateFromFileName($file));

        if (! $this->isValidLogType($type)) {
            $type = $this->getTypeFromFileName($file) ?? 'error';
        }

        ApiActionLogger::info(
            'AdminLogController::show',
            '管理者ログファイル詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'file' => $file,
                'type' => $type,
                'date' => $date,
            ]
        );

        $content = File::get($path);

        ApiActionLogger::info(
            'AdminLogController::show',
            '管理者ログファイル詳細取得完了',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'file' => $file,
                'type' => $type,
                'date' => $date,
                'size' => $this->formatBytes(File::size($path)),
            ]
        );

        return view('admin.logs.show', [
            'file' => $file,
            'content' => $content,
            'size' => $this->formatBytes(File::size($path)),
            'type' => $type,
            'typeLabel' => $this->getTypeLabel($type),
            'date' => $date,
        ]);
    }

    /**
     * ログ種別一覧
     */
    private function logTypes(): array
    {
        return [
            'error' => 'エラーログ',
            'laravel' => '通常ログ',
        ];
    }

    /**
     * 有効なログ種別か確認する
     */
    private function isValidLogType(?string $type): bool
    {
        return in_array($type, array_keys($this->logTypes()), true);
    }

    /**
     * ログ種別の表示名を取得する
     */
    private function getTypeLabel(?string $type): string
    {
        return $this->logTypes()[$type] ?? 'エラーログ';
    }

    /**
     * 許可するログファイル名か確認する
     */
    private function isValidLogFileName(string $file): bool
    {
        return preg_match('/^laravel-\d{4}-\d{2}-\d{2}\.log$/', $file) === 1
            || preg_match('/^error-\d{4}-\d{2}-\d{2}\.log$/', $file) === 1
            || $file === 'laravel.log'
            || $file === 'error.log';
    }

    /**
     * ファイル名からログ種別を取得する
     */
    private function getTypeFromFileName(string $file): ?string
    {
        if ($file === 'error.log' || preg_match('/^error-\d{4}-\d{2}-\d{2}\.log$/', $file) === 1) {
            return 'error';
        }

        if ($file === 'laravel.log' || preg_match('/^laravel-\d{4}-\d{2}-\d{2}\.log$/', $file) === 1) {
            return 'laravel';
        }

        return null;
    }

    /**
     * ファイル名から日付を取得する
     */
    private function getDateFromFileName(string $file): ?string
    {
        if (preg_match('/^(error|laravel)-(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches)) {
            return $matches[2];
        }

        return null;
    }

    /**
     * 種別と日付からログファイル名を作成する
     */
    private function makeLogFileName(string $type, string $date): string
    {
        return match ($type) {
            'laravel' => "laravel-{$date}.log",
            default => "error-{$date}.log",
        };
    }

    /**
     * ファイルサイズを見やすく変換する
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return round($bytes / 1024 / 1024, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
