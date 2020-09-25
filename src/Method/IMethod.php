<?php
namespace tingyu\HttpRequest\Method;

interface IMethod
{
    /**
     * 发起请求
     * @param string $requestUrl  请求地址
     * @param string|array|object $body  请求提
     * @return mixed
     */
    function request($requestUrl, $body);

}