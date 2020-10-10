<?php
namespace tingyu\HttpRequest\Method;

interface IMethod
{
    /**
     * 发起请求
     * @param string $requestUrl  请求地址
     * @param string|array $param  地址携带参数
     * @return mixed
     */
    function request($requestUrl, $param);

}