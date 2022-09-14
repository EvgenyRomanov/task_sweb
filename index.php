<?php


use classes\ApiSwebRu;
use JsonRPC2\Exception\AccessDeniedJsonException;
use JsonRPC2\Response\ExtendedResult\Error;

require_once "config.php";



$api = new ApiSwebRu();

try {
    $token = $api->getToken("eromanov93", "kVFTEVRSL@H6GeN4");
} catch (AccessDeniedJsonException $e) {
    $token = "ошибка авторизации | {$e->getMessage()}";
} catch (Exception $e) {
    $token = "ошибка при генерации токена | {$e->getMessage()}";
} 

try {
    $result = $api->move("asxcf345yhgg.ru", "none");
    $result = ($result instanceof Error) ? "ошибка при добавлении домена" : "домен добавлен";
} catch (Exception $e) {
    $result = "домен не добавлен | {$e->getMessage()}";
}

echo "token: $token";
echo "<br>";
echo "результат добавления домена: $result";