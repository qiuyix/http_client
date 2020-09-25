<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class RequestPatch  extends HttpClient implements IMethod
{
    public function request($requestUrl, $body = [])
    {
        $this->requestUrl = $requestUrl;

        $this->requestBody = array_merge($this->requestBody, $body);

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "PATCH");

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        return $this->responseBody;
    }

}