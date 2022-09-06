<?php
/**
 * Описание тестового задания:
 *
 * Очень богатый, с утончённым эстетическим вкусом престарелый наркобарон (в отставке) желает хранить свои трудовые капиталы
 * не только прогрессивно и современно, но и изысканно. В связи с этим барон заказал разработку веб-приложения для генерации
 * красивых адресов Bitcoin. Пока команда бекенд-разработчиков отлаживает серверный скрипт генерации ключей, предлагается
 * сверстать и закодить фронтенд одностраничного приложения, которое бы удовлетворило придирчивого заказчика.
 *
 * Визуальное оформление не должно расстроить барона, который и в лучшие свои годы был склонен к снобизму, а к старости
 * и подавно стал не сахар. Точно известно, что он много лет пользуется продукцией Apple. Безопасным оформлением,
 * чтобы было не стыдно показать заказчику, будет спокойный современный светлый стиль, без изысков. Приветствуется не слишком
 * отвлекающая анимация действий. Используйте любые CSS и JS фреймворки, библиотеки, шрифты, иконки, фон, никаких ограничений.
 *
 * Серверные разработчики обещают предоставить простой публичный API без авторизации. Есть URL, который параметров не принимает,
 * отдаёт ответ 200 и JSON вида:
 *
 * {"addr":"1ay5r8rLseh1qLYWAU21u8ePcyaWoaaPs","pkey":"Ky1smsug5HUpwDaDWZHvoz4vA3qVXvzegY8ky6m7RrWH9j8zx2zU"}
 *
 * JSON содержит ровно один Bitcoin адрес (26-35 символов) и приватный ключ к нему (51-52 символа). Сервер пока не готов,
 * но имеется простенький PHP скрипт, который моделирует работу такого API: файл btcaddr.php в аттаче.
 *
 * Логика работы одностраничного приложения ожидается следующая. Единственная кнопка на странице отправляет запрос на сервер и,
 * не дожидаясь ответа, рисует блок с loading-крутилкой. По факту ответа от сервера контент блока меняется, и вместо крутилки
 * показывает только что созданный адрес.
 *
 * Генерация адреса длится несколько секунд. Одновременно можно запустить несколько параллельных запросов,
 * но нельзя больше 3 — это важное условие! Нужно позволять генерировать несколько адресов параллельно,
 * но блокировать кнопку на время, пока на экране уже висит три крутилки.
 *
 * При серверной ошибке (нет соединения, или сервер вернул код ответа не 200, или не JSON, или JSON не содержит ключей addr и pkey),
 * следует отправить подробности в консоль браузера, а юзеру в блоке вместо крутилки показать общее сообщение,
 * что что-то пошло не так. Тестовая версия API возвращает разные ошибки случайным образом на 3 из 10 запросов.
 */

// Main function
function apiRun() {

    // This script randomly generates errors 3 out of 10 requests.
    // Uncomment following line to make all requests finish successfully.
    //$divine_intervention = 0;

    // Comment the following line to remove delay, or edit increase wait time.
    sleep(mt_rand(1, 3));

    // Randomly decide whether this request terminates successfully
    // or ends up with an error
    if (!isset($divine_intervention)) {
        $divine_intervention = mt_rand(0, 9);
    }

    switch($divine_intervention) {
        case 9:
            // Reply with a non-200 status code and no body
            $code = 400 + 100*mt_rand(0, 1) + mt_rand(0, 20);
            $proto = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($proto.' '.$code.' Webmaster Is Drunk');
            exit;
        case 8:
            // Reply with code 200 but no valid JSON.
            header('Content-Type: text/html; charset=utf-8');
            echo "This is most certainly not a JSON response. Sorry.";
            exit;
        case 7:
            // Reply with JSON but include no data.
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(array(
                'error' => 'Вы что, не видите, у нас обед! (╯°□°）╯︵ ┻━┻',
            ));
            exit;
        default:
            // Okay, no kidding, give them proper response this time
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(createBitaddress());
            break;
    }
}

// Creates valid API response
function createBitaddress() {
    return array(
        'addr' => addressify('7 do not use this address', mt_rand(26, 35)),
        'pkey' => addressify('this is not a bitcoin key', mt_rand(51, 52)),
    );
}

// Turns input into something resembling
// a not-really-base58-formatted string of given length
function addressify($input, $total_length) {
    $result = '';

    // Add all symbols from input
    $input = preg_replace('~[^a-z0-9]~', '', $input);
    for ($i = 0; $i < strlen($input); $i++) {
        if (mt_rand(0, 1)) {
            $result .= strtoupper($input{$i});
        } else {
            $result .= strtolower($input{$i});
        }
    }

    // Add random chars at the end
    while (strlen($result) < $total_length) {
        $result .= getRandomChar();
    }

    return $result;
}

// Random alphanumeric char
function getRandomChar() {
    $base = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return $base{mt_rand(0, strlen($base)-1)};
}

apiRun();
