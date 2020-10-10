<?php
namespace tingyu\HttpRequest;

use tingyu\HttpRequest\Request\IRequest;
use tingyu\HttpRequest\Request\RequestArray;
use tingyu\HttpRequest\Response\IResponse;

class HttpClient
{
    // 请求方式
    protected $request;

    // 响应方式
    protected $response;

    // curl 句柄
    protected $handler;

    // 错误号
    protected $errNo;

    // 错误信息
    protected $errMsg;

    // 请求地址
    protected $requestUrl;

    // 请求cookie array
    private $requestCookies;

    // 上传文件
    protected $requestFile;

    // 请求body体
    protected $requestBody;

    // 请求浏览器标识
    private $requestUserAgent;

    // 请求超时时间
    private $requestSecond;

    // 请求超时时间 毫秒
    private $requestMsSecond;

    // 请求头 array
    private $requestHeader;

    // https 请求是否进行验证
    private $requestSslVerifyPeer;

    // 响应头信息
    private $responseHeader;

    // 响应结果
    protected $responseBody;

    // 响应cookie信息 数组
    private $responseCookies;

    // 响应的状态码信息
    protected $responseStatusCode;

    public function __construct(IRequest $request = null, IResponse $response = null)
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('curl 扩展没有安装');
        }

        $handler = curl_init();
        if ($handler === false) {
            $this->getErrNo();
            $this->getErrMsg();
            throw new \Exception('初始化curl错误');
        }

        $this->requestUrl = null;
        $this->requestBody = [];
        $this->requestFile = [];

        $this->handler = $handler;

        // 注入请求体处理类
        $this->request = $request;

        // 注入响应体处理类
        $this->response = $response;

        $this->initialize();
    }

    /**
     * 初始花参数
     * @throws \Exception
     */
    private function initialize()
    {
        // 设置默认的请求配置
        $curlVersion = curl_version();
        $userAgent = "(".PHP_OS.") PHP/".PHP_VERSION." CURL/".$curlVersion['version'];
        $this->setRequestUserAgent($userAgent);

        // 设置某人的超时时间
        $this->setRequestTimeOut(30);

        // 设置默认是否校验https证书
        $this->setRequestSslVerifyPeer(false);

        // 设置请求结果不直接输出，而是返回数据
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);

        // 获取响应头部及响应的cookie
        curl_setopt($this->handler, CURLOPT_HEADERFUNCTION, [$this, 'dealResponseHeader']);
    }

    /**
     * 请求地址处理
     * @param $param
     */
    protected function setRequestUrl($requestUrl, $param)
    {
        if (is_array($param) && !empty($param)) {
            $this->requestBody = $param;
            $queryParam = http_build_query($param);
        } elseif (is_string($param) && $param != '') {
            $queryParam = $param;
            $this->requestBody[] = $param;
        }

        if (isset($queryParam)) {
            if (strpos($requestUrl, '?') === false) {
                $requestUrl .= '?' . $queryParam;
            } else {
                $requestUrl .= '&' . $queryParam;
            }
        }

        $this->requestUrl = $requestUrl;
    }


    /**
     * 设置请求携带的cookie值
     * @param string $key
     * @param string $value
     * @throws \Exception
     */
    public function setRequestCookie($key, $value)
    {
        if (!is_string($key) || !is_string($value)) {
            throw new \Exception('cookie的设置值非法');
        }

        if ($key == '') {
            throw new \Exception('cookie的键值不能为空');
        }

        $this->requestCookies[$key] = $value;
        $this->buildCookie();
    }


    /**
     * SSL PEM 证书验证
     * @param string $sslCertPath ssl证书绝对路径
     * @param string $sslKeyPath ssl密钥结对路径
     * @throws \Exception
     */
    public function setRequestCertificate($sslCertPath, $sslKeyPath)
    {
        if (strrchr($sslCertPath, '.pem') !== '.pem') {
            throw new \Exception('SSL CERT证书不是pem格式的文件');
        }

        if (!file_exists($sslCertPath)) {
            throw new \Exception('SSL CERT证书文件不存在，请传入绝对路径');
        }

        if (strrchr($sslKeyPath, '.pem') !== '.pem') {
            throw new \Exception('SSL KEY证书不是pem格式的文件');
        }

        if (!file_exists($sslKeyPath)) {
            throw new \Exception('SSL KEY证书文件不存在，请传入绝对路径');
        }

        curl_setopt($this->handler,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($this->handler,CURLOPT_SSLCERT, $sslCertPath);
        curl_setopt($this->handler,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($this->handler,CURLOPT_SSLKEY, $sslKeyPath);
    }

    /**
     * 设置https请求的时候是否进行证书校验
     * @param bool $bool
     * @param string $cacertPemPath  当进行证书校验的时候，应该加载证书的地址，证书可从https://curl.haxx.se/ca/cacert.pem下载
     * @throws \Exception
     */
    public function setRequestSslVerifyPeer($bool, $cacertPemPath = '')
    {
        if ($bool) {
            if (!is_string($cacertPemPath) || $cacertPemPath == '') {
                throw new \Exception('ssl证书地址格式不正确');
            }

            if (!file_exists($cacertPemPath)) {
                throw new \Exception('ssl证书文件不存在');
            }
        }

        $this->requestSslVerifyPeer = $bool;
        curl_setopt($this->handler,CURLOPT_SSL_VERIFYPEER, $bool);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYHOST, $bool);
        if ($bool) {
            curl_setopt($this->handler, CURLOPT_CAINFO, $cacertPemPath);
        }
    }

    /**
     * 设置请求标识 UA
     * @param string $userAgent
     */
    public function setRequestUserAgent($userAgent)
    {
        if (!is_string($userAgent)) {
            throw new \Exception('UserAgent值类型非法');
        }

        $this->requestUserAgent = $userAgent;

        curl_setopt($this->handler,CURLOPT_USERAGENT, $userAgent);
    }

    /**
     * 设置超时时间， 单位秒
     * @param int $second 超时时间（秒）
     * @throws \Exception
     */
    public function setRequestTimeOut($second)
    {
        if (!is_int($second) || $second < 0) {
            throw new \Exception('设置超时时间不合法');
        }

        $this->requestSecond = $second;
        curl_setopt($this->handler, CURLOPT_TIMEOUT, $second);
    }

    /**
     * 设置毫秒超时时间
     * @param int $msSecond
     * @throws \Exception
     */
    public function setRequestTimeoutMs($msSecond)
    {
        if (!is_int($msSecond) || $msSecond < 0) {
            throw new \Exception('设置毫秒超时时间不合法');
        }

        $this->requestMsSecond = $msSecond;
        curl_setopt($this->handler, CURLOPT_TIMEOUT_MS, $msSecond);
    }

    /**
     * 设置代理
     * @param string $proxyHost  代理地址
     * @param int $proxyPort    代理端口
     */
    public function setRequestProxy($proxyHost, $proxyPort)
    {
        curl_setopt($this->handler,CURLOPT_PROXY, $proxyHost);
        curl_setopt($this->handler,CURLOPT_PROXYPORT, $proxyPort);
    }

    /**
     * 设置请求头信息
     * @param string $key 键 content-type
     * @param string $value 值 text/html; charset=utf-8
     * @throws \Exception
     */
    public function setRequestHeader($key, $value)
    {
        if (!is_string($key) || !is_string($value)) {
            throw new \Exception('header键或值类型错粗');
        }

        if ($key == '') {
            throw new \Exception("header的键值不能为空");
        }

        $this->requestHeader[$key] = $value;
        $this->buildHeader();
    }

    /**
     * 获取curl错误编号
     * @return mixed
     */
    public function getErrNo()
    {
        $this->errNo = curl_errno($this->handler);
        return $this->errNo;
    }

    /**
     * 获取curl错误信息
     * @return mixed
     */
    public function getErrMsg()
    {
        $this->errMsg = curl_error($this->handler);
        return $this->errMsg;
    }

    /**
     * 获取响应的http状态码信息
     * @return mixed
     */
    public function getResponseStatusCode()
    {
        return $this->responseStatusCode;
    }

    /**
     * 获取响应头部信息
     * @param bool $isString true 字符串形式返回， false 数组形式返回（默认值）
     * @return array|string
     */
    public function getResponseHeader($isString = false) {
        $this->responseHeader = trim($this->responseHeader, PHP_EOL);
        if ($isString) {
            return $this->responseHeader;
        }

        $result = [];
        $header = explode(PHP_EOL, $this->responseHeader);
        foreach ($header as $value) {
            if ($value != '' && strpos($value, ":")) {
                $value = explode(': ', $value);
                if (array_key_exists($value[0], $result)) {
                    if (is_array($result[$value[0]])) {
                        $result[$value[0]][] = $value[1];
                    } else {
                        $oldValue = $result[$value[0]];
                        $result[$value[0]] = [
                            $oldValue,
                            $value[1]
                        ];
                    }
                } else {
                    $result[$value[0]] = $value[1];
                }
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * 获取响应cookie信息
     * @return mixed
     */
    public function getResponseCookie() {
        return $this->responseCookies;
    }

    /**
     * 获取原始响应内容
     * @return mixed
     */
    public function getResponseBody() {
        return $this->responseBody;
    }

    /**
     * 构建cookie
     */
    private function buildCookie()
    {
        $cookie = '';
        foreach ($this->requestCookies as $key=>$value) {
            $cookie .= "{$key}={$value};";
        }

        if ($cookie != '') {
            $cookie = rtrim($cookie, ';');

            curl_setopt($this->handler, CURLOPT_COOKIE, $cookie);
        }
    }

    /**
     * 构建header
     */
    private function buildHeader()
    {
        $header = [];
        foreach ($this->requestHeader as $key => $value) {
            $header[] = "{$key}:{$value}";
        }

        curl_setopt($this->handler, CURLOPT_HTTPHEADER, $header);
    }

    /**
     * 处理响应的header头部回调函数
     * 设置一个回调函数，这个函数有两个参数，第一个是cURL的资源句柄，第二个是输出的header数据。header数据的输出必须依赖这个函数，返回已写入的数据大小。
     * @param resource $handler curl资源句柄
     * @param string $header 响应的头部信息
     * @return int 返回响应头部的长度
     */
    public function dealResponseHeader($handler, $header)
    {
        // 获取头部信息
        $this->responseHeader .= $header;

        // 解析响应的cookie信息
        if (preg_match('/^Set-Cookie:\s*([^=]+)=([^;]+)/mi', $header, $cookie) === 1) {
            $this->responseCookies[$cookie[1]] = trim($cookie[2], " \n\r\t\0\x0B");
        }

        return strlen($header);
    }

    public function __destruct()
    {
        // 尝试做一些日志的请求
        // TODO: Implement __destruct() method.
    }

}