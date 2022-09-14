<?php

namespace JsonRPC2\Response\ExtendedResult;


class Error
{
    private $code;
    private $message;
    private $data;

    /**
     * Конструктор
     * 
     * @param string $code код выполнения операции (1 или 0)
     * @param string $message сообщение о результате
     * @param array дополнительные данные
     * 
     * @return void
     */
    public function __construct(string $code, string $message, array $data)
    {
        $this->code = (int)$code;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Возврящает код операции
     * 
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Возвращает сообщение о результате
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Возвращает дополнительные данные
     * 
     * @return array
     */
    public function getDate()
    {
        return $this->data;
    }
}