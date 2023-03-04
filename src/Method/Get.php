<?php


namespace tingyu\HttpRequest\Method;


use tingyu\HttpRequest\HttpClient;

class Get extends HttpClient
{
    public function do(string $uri, $data)
    {
        if (is_array($data) && !empty($data)) {
            $this->requestBody = $data;
            $queryParam = http_build_query($data);
        } elseif (is_string($data) && $data != '') {
            $queryParam = $data;
            $this->requestBody[] = $data;
        }

        if (isset($queryParam)) {
            if (strpos($uri, '?') === false) {
                $uri .= '?' . $queryParam;
            } else {
                $uri .= '&' . $queryParam;
            }
        }

        $this->requestUrl = $uri;

        curl_setopt($this->handler, CURLOPT_URL, $this->requestUrl);

        $this->responseBody = curl_exec($this->handler);

        $this->responseStatusCode = curl_getinfo($this->handler, CURLINFO_HTTP_CODE);

        curl_close($this->handler);

        return $this->responseBody;
    }

}