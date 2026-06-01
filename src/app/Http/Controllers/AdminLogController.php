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
        $date = $request->input('date');

        ApiActionLogger::info(
            'AdminLogController::index',
            '管理者ログファイル一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'date' => $date,
            ]
        );

        $logFiles = collect(File::files(storage_path('logs')))
            ->filter(function ($file) {
                return $this->isValidLogFileName($file->getFilename());
            })
            ->when($date, function ($files) use ($date) {
                return $files->filter(function ($file) use ($date) {
                    return $file->getFilename() === "laravel-{$date}.log";
                });
            })
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->map(function ($file) {
                return [
                    'name' => $file->getFilename(),
                    'date' => $this->getDateFromFileName($file->getFilename()),
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
                'date' => $date,
                'log_file_count' => $logFiles->count(),
            ]
        );

        return view('admin.logs.index', [
            'logFiles' => $logFiles,
            'date' => $date,
        ]);
    }

    /**
     * ログファイル詳細
     */
    public function show(string $file)
    {
        ApiActionLogger::info(
            'AdminLogController::show',
            '管理者ログファイル詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'file' => $file,
            ]
        );

        abort_unless($this->isValidLogFileName($file), 404);

        $path = storage_path('logs/' . $file);

        abort_unless(File::exists($path), 404);

        $content = File::get($path);

        ApiActionLogger::info(
            'AdminLogController::show',
            '管理者ログファイル詳細取得完了',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'file' => $file,
                'size' => $this->formatBytes(File::size($path)),
            ]
        );

        return view('admin.logs.show', [
            'file' => $file,
            'content' => $content,
            'size' => $this->formatBytes(File::size($path)),
        ]);
    }

    /**
     * 許可するログファイル名か確認する
     */
    private function isValidLogFileName(string $file): bool
    {
        return preg_match('/^laravel-\d{4}-\d{2}-\d{2}\.log$/', $file) === 1
            || $file === 'laravel.log';
    }

    /**
     * ファイル名から日付を取得する
     */
    private function getDateFromFileName(string $file): ?string
    {
        if (preg_match('/^laravel-(\d{4}-\d{2}-\d{2})\.log$/', $file, $matches)) {
            return $matches[1];
        }

        return null;
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
