<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;
use tingyu\HttpRequest\Util\Json;

class Post extends HttpClient
{
    private static $method = 'POST';

    public function do(string $uri, $data = [])
    {
        $this->requestUrl = $uri;
        $this->requestMethod = self::$method;

        if (is_array($data) || is_object($data)) {
            $this->requestBody = http_build_query($data);
        } else if (Json::IsJsonStr($data)) {
            $this->setHeader("content-type", "content-type: application/json;charset=UTF-8");
            $this->requestBody = $data;
        } else {
            $this->requestBody = $data;
        }

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, $this->requestMethod);

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        // POST数据
        curl_setopt($this->handler, CURLOPT_POST, true);

        // 把post的变量加上
        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);


        $this->responseBody = curl_exec($this->handler);

        $this->requestFile = [];

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        // todo 记录日志信息

        return $this->responseBody;
    }

}