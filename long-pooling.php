<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Makhnanov\Telegram81\Api\B;

(Dotenv::createUnsafeMutable(__DIR__))->load();

$bot = B::api((getenv('BOT_TOKEN') ?: throw new Exception('BOT_TOKEN empty')));
$channelId = '-100' . (getenv('CHANNEL_ID') ?: throw new Exception('CHANNEL_ID empty'));
$groupId = (int)('-100' . (getenv('GROUP_ID') ?: throw new Exception('GROUP_ID empty')));

while (true) {
    try {
        $updates = $bot->getUpdates(timeout: 60);
//        dump(date('H:i:s'));
        foreach ($updates as $update) {
            if (
                $update->isPrivateMessage()
                || !$update->message
                || $update->message->chat->id !== $groupId
            ) {
//                dump('skip');
                continue;
            }
            if (
                ($count = str_word_count($update->message->text, characters: PHP_EOL))
                && $count >= 17
            ) {
                sendChannelNotification();
            }
        }
    } catch (Throwable $th) {
//        dump($th->getMessage());
        sleep(10);
    }
    sleep(10);
}

function sendChannelNotification(): void
{
    global $bot, $channelId;
    $bot->sendMessage(
        $channelId,
        'Внимание!' . PHP_EOL . 'Возможно, начилась запись на новую ближайшую игру:',
        reply_markup: [
            'inline_keyboard' => [
                [
                    [
                        'text' => 'Проверить группу',
                        'url' => 'https://t.me/+ByceW9EsBmQ2Yjgy',
                    ]
                ]
            ]
        ]
    );
}
