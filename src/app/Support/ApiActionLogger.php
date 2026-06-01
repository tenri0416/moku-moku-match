<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class ApiActionLogger
{
    /**
     * API操作ログを laravel.log に出力する
     *
     * @param string $methodName メソッド名
     * @param string $message ログメッセージ
     * @param array $params パラメータ
     * @return void
     */
    public static function info(string $methodName, string $message, array $params = []): void
    {
        Log::info(PHP_EOL . self::formatLog(
            methodName: $methodName,
            message: $message,
            params: $params
        ));
    }

    /**
     * ログの表示形式を整える
     *
     * @param string $methodName
     * @param string $message
     * @param array $params
     * @return string
     */
    private static function formatLog(string $methodName, string $message, array $params): string
    {
        return implode(PHP_EOL, [
            '================ API操作ログ ================',
            'メソッド名：' . trim($methodName),
            'URL：' . request()->fullUrl(),
            'message：' . $message,
            'param：',
            self::formatParams(self::maskParams($params)),
            '===========================================',
        ]);
    }

    /**
     * パラメータを見やすく整形する
     *
     * @param array $params
     * @return string
     */
    private static function formatParams(array $params): string
    {
        if (empty($params)) {
            return '  なし';
        }

        $lines = [];

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = json_encode(
                    $value,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                );
            }

            $lines[] = '  ' . $key . '：' . $value;
        }

        return implode(PHP_EOL, $lines);
    }

    /**
     * パスワードなどログに出したくない値をマスクする
     *
     * @param array $params
     * @return array
     */
    private static function maskParams(array $params): array
    {
        $maskKeys = [
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'refresh_token',
            'authorization',
        ];

        foreach ($maskKeys as $key) {
            if (isset($params[$key])) {
                $params[$key] = '********';
            }
        }

        return $params;
    }
}
