<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;
use function Couchbase\defaultDecoder;

class Post extends HttpClient implements IMethod
{
    public function request($requestUrl, $param = null)
    {
        $this->setRequestUrl($requestUrl, $param);

        if (!is_null($this->request)) {
            $this->requestBody = $this->request->getData();
            $this->setRequestHeader('Content-Type', $this->request->getContentType());
        }

        curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "POST");

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        // POST数据
        curl_setopt($this->handler, CURLOPT_POST, true);

        // 把post的变量加上
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