<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class Patch extends HttpClient implements IMethod
{
    public function request($requestUrl, $param = null)
    {
        $this->setRequestUrl($requestUrl, $param);

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "PATCH");

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        curl_setopt($this->handler, CURLOPT_POSTFIELDS, $this->requestBody);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        if ($this->response == null) {
            return $this->responseBody;
        } else {
            return $this->response->parse($this->responseBody);
        }
    }

}