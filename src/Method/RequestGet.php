<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class RequestGet extends HttpClient implements IMethod
{
    public function request($requestUrl, $body = [])
    {
        if (is_array($body) && !empty($body)) {
            $this->requestBody = $body;
            $queryParam = http_build_query($body);
        } elseif (is_string($body) && $body != '') {
            $queryParam = $body;
            $this->requestBody[] = $body;
        }

        if (isset($queryParam)) {
            if (strpos($requestUrl, '?') === false) {
                $requestUrl .= '?' . $queryParam;
            } else {
                $requestUrl .= '&' . $queryParam;
            }
        }

        $this->requestUrl = $requestUrl;

        curl_setopt($this->handler, CURLOPT_URL, $requestUrl);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        return $this->responseBody;
    }

}