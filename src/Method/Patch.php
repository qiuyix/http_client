<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

/**
 * @deprecated
 */
class Patch  extends HttpClient
{
    private static $method = "PATCH";

    public function do(string $uri, $data = [])
    {
        $this->requestUrl = $uri;
        $this->requestMethod = self::$method;

        $this->requestBody = array_merge($this->requestBody, $data);

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, $this->requestMethod);

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        // todo 记录日志信息

        return $this->responseBody;
    }

}