<?php

namespace classes;

use JsonRPC2\Exception\AccessDeniedJsonException;
use JsonRPC2\Response\ExtendedResult\Error;


class ApiSwebRu
{

    private $notAuthorizedUrl;
    private $authorizedUrl;
    private $token;
    private $jsonrpc;
    private $version;


    /**
     * Конструктор
     * 
     * @param string $notAuthorizedUrl url для доступа к объектам без авторизации
     * @param string $authorizedUrl url для доступа к объектам с авторизацией
     * @param string $jsonrpc текущая версия JSON-RPC
     * @param string $version текущая версия приложения
     * 
     * @return void
     */
    public function __construct(
        string $notAuthorizedUrl = 'https://api.sweb.ru/notAuthorized', 
        string $authorizedUrl = 'https://api.sweb.ru/domains',
        string $jsonrpc = "2.0",
        string $version = "1.147.20220912145459"
    ) {
        $this->notAuthorizedUrl = $notAuthorizedUrl;
        $this->authorizedUrl = $authorizedUrl;
        $this->token = "";
        $this->jsonrpc = $jsonrpc;
        $this->version = $version;
    }

    /**
     * Получение авторизационного токена по логину и паролю
     * 
     * @param string $login логин
     * @param string $password пароль
     * 
     * @return string токен
     */
    public function getToken(string $login, string $password) : string
    {
        $data = [
            "jsonrpc" => $this->jsonrpc,
            "method" => "getToken",
            "params" => [
                "login" => $login,
                "password" => $password
            ]
        ];

        $data = json_encode($data);

        $curlHandler = curl_init($this->notAuthorizedUrl);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curlHandler, CURLOPT_POST, 1);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_HEADER, false);
        $result = curl_exec($curlHandler);
        curl_close($curlHandler);
         
        $result = json_decode($result, true);

        if (array_key_exists("error", $result)) {
            if ($result['error']['code'] === -32400) {
                throw new AccessDeniedJsonException($result['error']['message']);
            } else {
                throw new \Exception($result['error']['message']);
            }
        }

        $token = $result["result"];
        $this->token = $token;

        return $token;
    }

    /**
     * Добавление домена на аккаунт
     * 
     * @param string $domain домен
     * @param string $prolongType тип продления ('none', 'bonus_money', 'manual')
     * @param string|null $dir домашняя директория
     * 
     * @return int|Error
     */
    public function move(string $domain, string $prolongType, string $dir = null) 
    {
        
        $data = [
            "jsonrpc" => $this->jsonrpc,
            "version" => $this->version,
            "method" => "add",
            "params" => [
                "domain" => $domain,
                "prolongType" => $prolongType,
            ],
            "user" => "eromanov93"
        ];

        if (!$dir) {
            $data["params"]["dir"] = $dir;
        }

        $data = json_encode($data);

        $curlHandler = curl_init($this->authorizedUrl);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, ['Content-Type:application/json', 
            "Authorization:Bearer {$this->token}"]); 
        curl_setopt($curlHandler, CURLOPT_POST, 1);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_HEADER, false);
        $result = curl_exec($curlHandler);
        curl_close($curlHandler);
        
        $result = json_decode($result, true);

        if (array_key_exists("error", $result)) {
            throw new \Exception($result['error']['message']);
        }

        if (array_key_exists('result', $result)) {
            if (array_key_exists('extendedResult', $result)) {
                if ($result['result']['extendedResult']['code'] === '1') {
                    return 1;
                } else {
                    $extendedResultCode = $result['result']['extendedResult']['code'];
                    $extendedResultMessage = $result['result']['extendedResult']['message'];
                    $extendedResultData = $result['result']['extendedResult']['data'];
                    
                    return new Error($extendedResultCode, $extendedResultMessage, $extendedResultData);
                }
            } 
        }

        throw new \Exception();
    }

    /**
     * Возвращает url объекта, доступного без авторизации
     * 
     * @return string url
     */
    public function getNotAuthorizedUrl() : string
    {
        return $this->notAuthorizedUrl;
    }

    /**
     * Возвращает url объекта, доступного c авторизацией
     * 
     * @return string url
     */
    public function getAuthorizedUrl() : string
    {
        return $this->authorizedUrl;
    }

    /**
     * Изменяет url объекта, доступного без авторизации
     * 
     * @param string $newNotAuthorizedUrl новый url
     * @return void
     */    
    public function setNotAuthorizedUrl(string $newNotAuthorizedUrl)
    {
        $this->notAuthorizedUrl = $newNotAuthorizedUrl;
    }

    /**
     * Изменяет url объекта, доступного с авторизацией
     * 
     * @param string $newAuthorizedUrl новый url
     * @return void
     */   
    public function setAuthorizedUrl(string $newAuthorizedUrl)
    {
        $this->authorizedUrl = $newAuthorizedUrl;
    }

    /**
     * Изменяет текущую версию JSON-RPC
     * 
     * @param string $newJsonrpc
     * @return void
     */   
    public function setJsonrpc(string $newJsonrpc)
    {
        $this->jsonrpc = $newJsonrpc;
    }

    /**
     * Изменяет текущую версию приложения
     * 
     * @param string $newVersion
     * @return void
     */   
    public function setVersion(string $newVersion)
    {
        $this->version = $newVersion;
    }
}