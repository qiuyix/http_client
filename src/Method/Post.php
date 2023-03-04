<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class Post extends HttpClient
{
    public function do(string $uri, $data = [])
    {
        $this->requestUrl = $uri;

        $this->requestBody = array_merge($this->requestBody, $data);

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        // POST数据
        curl_setopt($this->handler, CURLOPT_POST, true);

        // 把post的变量加上
        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);


        $this->responseBody = curl_exec($this->handler);

        $this->requestFile = [];

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        return $this->responseBody;
    }

}