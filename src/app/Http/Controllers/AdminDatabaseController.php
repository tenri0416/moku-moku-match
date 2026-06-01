<?php

namespace App\Http\Controllers;

use App\Support\ApiActionLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDatabaseController extends Controller
{
    /**
     * 非表示にするテーブル
     *
     * パスワード、セッション、トークン系は画面に出さない
     */
    private array $hiddenTables = [
        'password_reset_tokens',
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'personal_access_tokens',
    ];

    /**
     * テーブル一覧
     */
    public function index()
    {
        ApiActionLogger::info(
            'AdminDatabaseController::index',
            '管理者DBテーブル一覧画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
            ]
        );

        $tables = collect(Schema::getTables())
            ->map(function (array $table) {
                return $table['name'] ?? $table['table'] ?? null;
            })
            ->filter()
            ->reject(fn (string $table) => in_array($table, $this->hiddenTables, true))
            ->values();

        $tableCounts = $tables->mapWithKeys(function (string $table) {
            return [
                $table => DB::table($table)->count(),
            ];
        });

        ApiActionLogger::info(
            'AdminDatabaseController::index',
            '管理者DBテーブル一覧取得完了',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'table_count' => $tables->count(),
            ]
        );

        return view('admin.database.index', [
            'tables' => $tables,
            'tableCounts' => $tableCounts,
        ]);
    }

    /**
     * テーブルの中身を表示
     */
    public function show(Request $request, string $table)
    {
        ApiActionLogger::info(
            'AdminDatabaseController::show',
            '管理者DBテーブル詳細画面にアクセス',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'table' => $table,
                'page' => $request->query('page'),
            ]
        );

        abort_if(in_array($table, $this->hiddenTables, true), 404);

        $tables = collect(Schema::getTables())
            ->map(function (array $tableInfo) {
                return $tableInfo['name'] ?? $tableInfo['table'] ?? null;
            })
            ->filter()
            ->values();

        abort_unless($tables->contains($table), 404);

        $columns = collect(Schema::getColumns($table))
            ->pluck('name')
            ->values();

        $rows = DB::table($table)
            ->latest($this->getOrderColumn($table, $columns))
            ->paginate(30)
            ->withQueryString();

        ApiActionLogger::info(
            'AdminDatabaseController::show',
            '管理者DBテーブル詳細取得完了',
            [
                'admin_id' => Auth::guard('admin')->id(),
                'table' => $table,
                'column_count' => $columns->count(),
            ]
        );

        return view('admin.database.show', [
            'table' => $table,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }

    /**
     * 並び順に使うカラムを取得する
     */
    private function getOrderColumn(string $table, $columns): string
    {
        if ($columns->contains('created_at')) {
            return 'created_at';
        }

        if ($columns->contains('id')) {
            return 'id';
        }

        return $columns->first();
    }
}
