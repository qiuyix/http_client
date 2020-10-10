<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class Get extends HttpClient implements IMethod
{
    public function request($requestUrl, $param = null)
    {
        $this->setRequestUrl($requestUrl, $param);

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_setopt($this->handler, CURLINFO_HEADER_OUT, TRUE);

        curl_close($this->handler);

        if ($this->response == null) {
            return $this->responseBody;
        } else {
            return $this->response->parse($this->responseBody);
        }
    }

}