import json
import os
import shutil
import time
import traceback
import random
from datetime import datetime
from playwright.sync_api import sync_playwright, TimeoutError as PlaywrightTimeoutError

# ============================================================
# 確認対象URL
# ============================================================

ARTICLE_INDEX_URL = "https://mokumokumatch.top/articles"

DETAIL_URLS = [
    "https://mokumokumatch.top/articles/study-with-work-partner",
    "https://mokumokumatch.top/articles/how-to-start-working-alone",
    "https://mokumokumatch.top/reading",
    "https://mokumokumatch.top/start",
    "https://mokumokumatch.top/remote-lonely",
]

USER_AGENTS = [
    # iPhone Safari 系
    "Mozilla/5.0 (iPhone; CPU iPhone OS 13_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 16_5_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1",
    "Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1",

    # Android Chrome 系
    "Mozilla/5.0 (Linux; Android 7.1.1; vivo X20) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.45 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 10; Pixel 4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36",
    "Mozilla/5.0 (Linux; Android 13; Pixel 7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Mobile Safari/537.36",

    # Windows Chrome 系
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36",

    # Mac Chrome 系
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
]

# ============================================================
# ログ出力用
# CloudWatch Logsで日本語でも追いやすいようにする
# ============================================================

def log_info(step: str, message: str, data: dict | None = None) -> None:
    """
    日本語でCloudWatch Logsに出すための共通ログ関数。
    print() の内容はCloudWatch Logsに表示される。
    """
    payload = {
        "時刻": datetime.utcnow().isoformat() + "Z",
        "種別": "INFO",
        "工程": step,
        "メッセージ": message,
    }

    if data:
        payload["詳細"] = data

    print(json.dumps(payload, ensure_ascii=False))


def log_error(step: str, message: str, data: dict | None = None) -> None:
    """
    エラー用ログ。
    どの工程で失敗したか分かるようにする。
    """
    payload = {
        "時刻": datetime.utcnow().isoformat() + "Z",
        "種別": "ERROR",
        "工程": step,
        "メッセージ": message,
    }

    if data:
        payload["詳細"] = data

    print(json.dumps(payload, ensure_ascii=False))


# ============================================================
# Lambda / Playwright 実行準備
# ============================================================

def prepare_tmp_dirs() -> None:
    """
    Lambda上でChromiumが使う一時領域を準備する。

    注意：
    XDG_CACHE_HOME を /tmp/.cache に設定すると、
    Playwrightが /tmp/.cache/ms-playwright を探してしまう場合がある。

    今回はDockerfile側で Chromium を /ms-playwright に入れているため、
    PLAYWRIGHT_BROWSERS_PATH を /ms-playwright に固定する。
    """
    log_info("準備", "一時ディレクトリの準備を開始します。")

    os.environ["HOME"] = "/tmp"
    os.environ["PLAYWRIGHT_BROWSERS_PATH"] = "/ms-playwright"

    for path in [
        "/tmp/playwright",
        "/tmp/.config",
    ]:
        if os.path.exists(path):
            shutil.rmtree(path, ignore_errors=True)

        os.makedirs(path, exist_ok=True)

    log_info(
        "準備",
        "一時ディレクトリの準備が完了しました。",
        {
            "HOME": os.environ.get("HOME"),
            "PLAYWRIGHT_BROWSERS_PATH": os.environ.get("PLAYWRIGHT_BROWSERS_PATH"),
        },
    )
def get_random_user_agent() -> str:
    """
    User-Agent一覧からランダムに1つ選ぶ。
    """
    user_agent = random.choice(USER_AGENTS)

    log_info(
        "User-Agent選択",
        "今回使用するUser-Agentを選択しました。",
        {
            "user_agent": user_agent,
        },
    )

    return user_agent

def random_access_wait(min_seconds: float = 3.0, max_seconds: float = 15.0) -> None:
    """
    アクセス間隔にゆらぎを持たせるための待機処理。

    テスト環境への負荷を下げるため、
    各ページアクセス前に数秒〜十数秒のランダム待機を入れる。
    """
    wait_seconds = random.uniform(min_seconds, max_seconds)

    log_info(
        "アクセス待機",
        "次のページアクセスまでランダム待機します。",
        {
            "待機秒数": round(wait_seconds, 2),
            "最小秒数": min_seconds,
            "最大秒数": max_seconds,
        },
    )

    time.sleep(wait_seconds)

# ============================================================
# ページ操作
# ============================================================

def scroll_page(page, duration_seconds: int) -> None:
    """
    指定秒数をかけてページを下方向にスクロールする。
    例：duration_seconds=5 なら約5秒かけて下までスクロールする。
    """
    log_info(
        "スクロール",
        "スクロールを開始します。",
        {
            "スクロール秒数": duration_seconds,
        },
    )

    steps = 30
    sleep_time = duration_seconds / steps

    for _ in range(steps):
        page.evaluate(
            """
            () => {
                const scrollHeight =
                    document.body.scrollHeight ||
                    document.documentElement.scrollHeight ||
                    0;

                const step = Math.max(scrollHeight / 30, 300);
                window.scrollBy(0, step);
            }
            """
        )
        time.sleep(sleep_time)

    log_info(
        "スクロール",
        "スクロールが完了しました。",
        {
            "スクロール秒数": duration_seconds,
        },
    )


def get_like_button_info(page) -> dict:
    """
    いいねボタンが存在するか確認する。

    注意：
    本番のいいね数を不正に増やさないため、
    この処理ではクリックしない。
    """
    log_info("いいね確認", "いいねボタンの存在確認を開始します。")

    selector = "[data-article-like-button]"
    button_count = page.locator(selector).count()

    if button_count == 0:
        log_info(
            "いいね確認",
            "いいねボタンは見つかりませんでした。",
            {
                "like_button_exists": False,
            },
        )
        return {
            "like_button_exists": False,
            "like_count": None,
            "article_id": None,
            "clicked": False,  # 追加
        }

    button = page.locator(selector).first
    article_id = button.get_attribute("data-article-id")

    # --- [追加] いいねボタンをクリックする処理 ---
    try:
        # ボタンが画面外にある場合に備えてスクロールし、クリック
        button.scroll_into_view_if_needed()
        button.click()
        
        # Ajax（非同期通信）などの処理待ちとして、必要に応じて少し待機
        page.wait_for_timeout(1000) 
        clicked_successfully = True
    except Exception as e:
        log_info("いいね失敗", f"クリック時にエラーが発生しました: {e}")
        clicked_successfully = False
    # --------------------------------------------

    like_count_text = None
    count_locator = page.locator("[data-article-like-count]")

    if count_locator.count() > 0:
        # クリックした「後」の最新のカウントを取得
        like_count_text = count_locator.first.inner_text().strip()

    # 戻り値にクリック結果も含める
    return {
        "like_button_exists": True,
        "like_count": like_count_text,
        "article_id": article_id,
        "clicked": clicked_successfully,
    }


def open_and_scroll(page, url: str, duration_seconds: int) -> dict:
    """
    1ページ分の確認処理。

    やること：
    1. URLへアクセス
    2. HTTPステータス確認
    3. タイトル取得
    4. 指定秒数スクロール
    5. いいねボタンの存在確認
    """
    started_at = time.time()

    result = {
        "url": url,
        "status": None,
        "success": False,
        "title": None,
        "scroll_seconds": duration_seconds,
        "like_button_exists": False,
        "like_count": None,
        "article_id": None,
        "elapsed_seconds": None,
        "error": None,
    }

    log_info(
        "ページ確認",
        "ページ確認を開始します。",
        {
            "url": url,
            "スクロール秒数": duration_seconds,
        },
    )

    try:
        # ページへアクセス
        log_info(
            "ページアクセス",
            "URLへアクセスします。",
            {
                "url": url,
            },
        )

        response = page.goto(url, wait_until="domcontentloaded", timeout=45000)

        result["status"] = response.status if response else None
        result["title"] = page.title()

        log_info(
            "ページアクセス",
            "URLへのアクセスが完了しました。",
            {
                "url": url,
                "HTTPステータス": result["status"],
                "ページタイトル": result["title"],
            },
        )

        # ページ描画を少し待つ
        page.wait_for_timeout(1000)

        # スクロール実行
        scroll_page(page, duration_seconds)

        # いいねボタン確認
        like_info = get_like_button_info(page)
        result.update(like_info)

        # 200〜399なら成功扱い
        result["success"] = result["status"] is not None and 200 <= result["status"] < 400

        result["elapsed_seconds"] = round(time.time() - started_at, 2)

        if result["success"]:
            log_info(
                "ページ確認",
                "ページ確認が成功しました。",
                {
                    "url": url,
                    "HTTPステータス": result["status"],
                    "ページタイトル": result["title"],
                    "いいねボタン": result["like_button_exists"],
                    "いいね数": result["like_count"],
                    "処理秒数": result["elapsed_seconds"],
                },
            )
        else:
            log_error(
                "ページ確認",
                "ページ確認は失敗扱いです。HTTPステータスを確認してください。",
                {
                    "url": url,
                    "HTTPステータス": result["status"],
                    "処理秒数": result["elapsed_seconds"],
                },
            )

    except PlaywrightTimeoutError as e:
        result["elapsed_seconds"] = round(time.time() - started_at, 2)
        result["error"] = f"timeout: {str(e)}"

        log_error(
            "ページ確認",
            "ページ確認中にタイムアウトしました。",
            {
                "url": url,
                "エラー": result["error"],
                "処理秒数": result["elapsed_seconds"],
            },
        )

    except Exception as e:
        result["elapsed_seconds"] = round(time.time() - started_at, 2)
        result["error"] = str(e)

        log_error(
            "ページ確認",
            "ページ確認中に予期しないエラーが発生しました。",
            {
                "url": url,
                "エラー": result["error"],
                "処理秒数": result["elapsed_seconds"],
            },
        )

    return result


# ============================================================
# Lambdaメイン処理
# ============================================================

def lambda_handler(event, context):
    """
    Lambdaの入口。

    処理内容：
    1. 一時ディレクトリ準備
    2. Chromium起動
    3. 記事一覧ページ確認
    4. 指定記事ページ確認
    5. 結果をJSONで返却
    """
    lambda_started_at = time.time()

    log_info(
        "Lambda開始",
        "記事ページの自動表示確認を開始します。",
        {
            "記事一覧URL": ARTICLE_INDEX_URL,
            "詳細ページ数": len(DETAIL_URLS),
        },
    )

    prepare_tmp_dirs()

    results = []
    browser = None

    try:
        with sync_playwright() as p:
            log_info("ブラウザ起動", "Chromiumの起動を開始します。")

            browser = p.chromium.launch(
                headless=True,
                chromium_sandbox=False,
                args=[
                    "--no-sandbox",
                    "--disable-setuid-sandbox",
                    "--disable-dev-shm-usage",
                    "--disable-gpu",
                    "--disable-software-rasterizer",
                    "--in-process-gpu",
                    "--disable-extensions",
                    "--disable-background-networking",
                    "--disable-background-timer-throttling",
                    "--disable-backgrounding-occluded-windows",
                    "--disable-renderer-backgrounding",
                    "--disable-sync",
                    "--disable-default-apps",
                    "--mute-audio",
                    "--no-first-run",
                    "--no-default-browser-check",
                    "--single-process",
                    "--no-zygote",
                    "--window-size=1280,720",
                ],
            )

            log_info("ブラウザ起動", "Chromiumの起動が完了しました。")

            selected_user_agent = get_random_user_agent()

            context = browser.new_context(
                user_agent=selected_user_agent,
                viewport={"width": 1280, "height": 720},
                extra_http_headers={
                    "Accept-Language": "ja-JP,ja;q=0.9,en-US;q=0.8,en;q=0.7",
                },
            )

            page = context.new_page()

            log_info(
                "ページ作成",
                "ブラウザページの作成が完了しました。",
                {
                    "user_agent": selected_user_agent,
                },
            )

            random_access_wait(3.0, 15.0)

            # 記事一覧ページを確認
            results.append(open_and_scroll(page, ARTICLE_INDEX_URL, 3))

            # 詳細ページを順番に確認
            for index, detail_url in enumerate(DETAIL_URLS, start=1):
                random_access_wait(3.0, 15.0)
                log_info(
                    "詳細ページ確認",
                    "詳細ページの確認を開始します。",
                    {
                        "現在の番号": index,
                        "全体件数": len(DETAIL_URLS),
                        "url": detail_url,
                    },
                )

                results.append(open_and_scroll(page, detail_url, 5))

            context.close()
            browser.close()
            browser = None

            log_info("ブラウザ終了", "Chromiumを正常に終了しました。")

        has_error = any(not result["success"] for result in results)
        elapsed_seconds = round(time.time() - lambda_started_at, 2)

        success_count = sum(1 for result in results if result["success"])
        error_count = len(results) - success_count

        if has_error:
            log_error(
                "Lambda終了",
                "一部のページ確認に失敗しました。",
                {
                    "成功件数": success_count,
                    "失敗件数": error_count,
                    "合計件数": len(results),
                    "総処理秒数": elapsed_seconds,
                },
            )
        else:
            log_info(
                "Lambda終了",
                "すべてのページ確認が成功しました。",
                {
                    "成功件数": success_count,
                    "失敗件数": error_count,
                    "合計件数": len(results),
                    "総処理秒数": elapsed_seconds,
                },
            )

        return {
            "statusCode": 500 if has_error else 200,
            "body": json.dumps(
                {
                    "message": "記事ページの表示確認が完了しました。",
                    "note": "いいねボタンは存在確認のみ行い、クリックはしていません。",
                    "summary": {
                        "success_count": success_count,
                        "error_count": error_count,
                        "total_count": len(results),
                        "elapsed_seconds": elapsed_seconds,
                    },
                    "results": results,
                },
                ensure_ascii=False,
            ),
        }

    except Exception as e:
        elapsed_seconds = round(time.time() - lambda_started_at, 2)

        try:
            if browser:
                context.close()
                browser.close()
        except Exception:
            pass

        log_error(
            "Lambda異常終了",
            "PlaywrightまたはChromiumの起動・実行中に失敗しました。",
            {
                "エラー": str(e),
                "総処理秒数": elapsed_seconds,
                "traceback": traceback.format_exc(),
            },
        )

        return {
            "statusCode": 500,
            "body": json.dumps(
                {
                    "message": "PlaywrightまたはChromiumの起動・実行中に失敗しました。",
                    "error": str(e),
                    "elapsed_seconds": elapsed_seconds,
                    "traceback": traceback.format_exc(),
                },
                ensure_ascii=False,
            ),
        }
