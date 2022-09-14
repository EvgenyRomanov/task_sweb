<?php

namespace tests;

use classes\ApiSwebRu;
use PHPUnit\Framework\TestCase;
use JsonRPC2\Exception\AccessDeniedJsonException;


class ApiSwebRuTest extends TestCase
{

    private $api;

    protected function setUp() : void
    {
        $this->api = new ApiSwebRu();
    }

    protected function tearDown() : void
    {
        $this->api = null;
    }

    public function testGetToken() : void
    {
        $result = $this->api->getToken("eromanov93", "kVFTEVRSL@H6GeN4");
        $this->assertEquals("string", gettype($result));
    
        try {
            $this->api->getToken("eromanov9", "kVFTEVRSL@H6GeN4");
            $this->fail('Не проброшено исключение на ошибку авторизации');
        } catch (AccessDeniedJsonException $e) {} 
    }

    /**
     * @dataProvider moveProvider
     */
    public function testMove($domain, $prolongType, $dir, $expected, $message, $exception) : void
    {
        if ($exception) {
            $this->expectException($exception);
        }
        $this->api->getToken("eromanov93", "kVFTEVRSL@H6GeN4");
        $result = $this->api->move($domain, $prolongType, $dir);
        $this->assertEquals($expected, gettype($result), $message);
    }

    public function moveProvider()
    { 
        return [
            'norm' => ["asxcf345yhgg.ru", "none", null, "integer", 'Корректные параметры', null],
            'exception' => ["asxcf345yhgg.ru", "none", null, "integer", 'Исключение', \Exception::class],
            'error' => ["asxcf345yhgg.ru", "1234", null, "object", 'Некорректные параметры', null],
        ];
    }

}