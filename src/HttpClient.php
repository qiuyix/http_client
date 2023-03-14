<?php

namespace tingyu\HttpRequest;

use tingyu\HttpRequest\Method\Delete;
use tingyu\HttpRequest\Method\Get;
use tingyu\HttpRequest\Method\Post;
use tingyu\HttpRequest\Method\Put;

/**
 * @method static Get($uri, $data)
 * @method static Post($uri, $data)
 * @method static Put($uri, $data)
 * @method static Delete($uri, $data)
 * @method static UploadFile($uri, $data)
 */
abstract class HttpClient
{
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

    // 请求的方法
    protected $requestMethod;

    // 请求浏览器标识
    private $requestUserAgent;

    // 请求超时时间
    private $requestSecond = 30;

    // 请求超时时间 毫秒
    private $requestMsSecond;

    // 请求头 array
    private $requestHeader;

    // https证书存放路径
    private $requestSslCaCert = [
        'verifyPeer' => false,
        'caCert' => '',
    ];

    // 证书请求
    private $requestSslCert;

    // 代理信息
    private $proxy;

    // 响应头信息
    public $responseHeader;

    // 响应结果
    protected $responseBody;

    // 响应cookie信息 数组
    public $responseCookies;

    // 响应的状态码信息
    public $responseStatusCode;

    /**
     * 验证 curl 扩展有无安装，设置默认请求参数
     * @throws \Exception
     */
    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('curl 扩展没有安装');
        }

        // 设置默认的请求配置
        $curlVersion = curl_version();
        $this->requestUserAgent = "(" . PHP_OS . ") PHP/" . PHP_VERSION . " CURL/" . $curlVersion['version'];
    }

    /**
     * curl 的初始化设置
     * @throws \Exception
     */
    protected function initialize()
    {
        $handler = curl_init();
        if ($handler === false) {
            throw new \Exception('初始化curl错误');
        }

        $this->handler = $handler;

        $this->buildCookie();
        $this->buildSslCert();
        $this->buildSslVerifyPeer();
        $this->buildUserAgent();
        $this->buildRequestTimeout();
        $this->buildHeader();
        $this->buildProxy();

        // 设置请求结果不直接输出，而是返回数据
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
        // 获取响应头部及响应的cookie
        curl_setopt($this->handler, CURLOPT_HEADERFUNCTION, [$this, 'dealResponseHeader']);
    }


    /**
     * 设置请求携带的cookie值
     * @param string $key
     * @param string $value
     * @return HttpClient
     */
    public function setCookie($key, $value)
    {
        if (!is_string($key) || !is_string($value)) {
            throw new \Exception('cookie的设置值非法');
        }

        if ($key == '') {
            throw new \Exception('cookie的键值不能为空');
        }

        $this->requestCookies[$key] = $value;
        return $this;
    }


    /**
     * SSL PEM 证书验证
     * @param string $sslCertPath ssl证书绝对路径
     * @param string $sslKeyPath ssl密钥结对路径
     * @return HttpClient
     * @throws \Exception
     */
    public function setCertificate($sslCertPath, $sslKeyPath)
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

        $this->requestSslCert['key'] = $sslKeyPath;
        $this->requestSslCert['cert'] = $sslCertPath;
        return $this;
    }


    /**
     * 设置https请求的时候是否进行证书校验
     * @param string $caCertPemFile 当进行证书校验的时候，应该加载证书的地址，证书可从https://curl.haxx.se/ca/cacert.pem下载
     * @return HttpClient
     * @throws \Exception
     */
    public function setSslVerifyPeer(string $caCertPemFile)
    {
        $isVerifyPeer = false;

        if (!empty($caCertPemFile)) {

            if (!file_exists($caCertPemFile)) {
                throw new \Exception('ssl证书文件不存在');
            } else {
                $this->requestSslCaCertFile = $caCertPemFile;
                $isVerifyPeer = true;
            }
        }

        $this->requestSslCaCert = [
            'verifyPeer' => $isVerifyPeer,
            'caCert' => $caCertPemFile,
        ];

        return $this;
    }


    /**
     * 设置请求标识 UA
     * @param string $userAgent
     * @throws \Exception
     * @return HttpClient
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent)) {
            throw new \Exception('UserAgent值类型非法');
        }
        $this->requestUserAgent = $userAgent;
        return $this;
    }


    /**
     * 设置超时时间， 单位秒
     * @param int $second 超时秒超时
     * @param int $msSecond 请求毫秒超时
     * @throws \Exception
     * @return HttpClient
     */
    public function setTimeOut($second, $msSecond = 0)
    {
        if (!is_int($second) || $second < 0) {
            throw new \Exception("超时时间：秒 值非法");
        }

        if (!is_int($msSecond) || $msSecond < 0) {
            throw new \Exception("超时时间：毫秒 值非法");
        }

        $this->requestSecond = $second;
        $this->requestMsSecond = $msSecond;

        return $this;
    }

    /**
     * 设置代理
     * @param string $proxyHost 代理地址
     * @param int $proxyPort 代理端口
     * @return HttpClient
     */
    public function setProxy($proxyHost, $proxyPort)
    {
        $this->proxy['host'] = $proxyHost;
        $this->proxy['port'] = $proxyPort;
        return $this;
    }

    /**
     * 设置请求头信息
     * @param string $key 键 content-type
     * @param string $value 值 text/html; charset=utf-8
     * @throws \Exception
     * @return HttpClient
     */
    public function setHeader($key, $value)
    {
        if (!is_string($key) || $key == '' || !is_string($value)) {
            throw new \Exception('请求头信息不正确');
        }

        $this->requestHeader[$key] = $value;

        return $this;
    }

    /**
     * 获取curl错误编号
     * @return mixed
     */
    public function getErrNo()
    {
        return $this->errNo;
    }

    /**
     * 获取curl错误信息
     * @return mixed
     */
    public function getErrMsg()
    {
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

    /**
     * 发起请求
     * @param string $uri 请求地址
     * @param string|array|object $data 请求提
     * @return mixed
     */
    abstract function do(string $uri, $data);


    /**
     * 添加上传文件
     * @param $filePath
     * @param $field
     * @param string $mimeType
     */
    public function setUploadFile($field, $filePath, $mimeType = '')
    {
        $filePath = trim($filePath);
        if ($filePath == '') {
            throw new \Exception('请传入文件绝对路径');
        }

        if (!file_exists($filePath)) {
            throw new \Exception('文件资源不存在');
        }

        if ($field == '') {
            throw new \Exception('字段名不能为空');
        }

        $info = pathinfo($filePath);
        $this->requestFile[$field] = new \CURLFile($filePath, $mimeType, $info['basename']);
    }

    /**
     * 构建请求 user-agent
     * @return void
     */
    private function buildUserAgent()
    {
        curl_setopt($this->handler, CURLOPT_USERAGENT, $this->requestUserAgent);
    }

    /**
     * 构建 ssl 证书请求
     * @return void
     */
    private function buildSslCert()
    {
        if (empty($this->requestSslCert)) {
            return;
        }
        curl_setopt($this->handler, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($this->handler, CURLOPT_SSLCERT, $this->requestSslCert['cert']);
        curl_setopt($this->handler, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($this->handler, CURLOPT_SSLKEY, $this->requestSslCert['key']);
    }

    /**
     * 构建 https 请求验证证书
     * @return void
     */
    private function buildSslVerifyPeer()
    {
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, $this->requestSslCaCert['verifyPeer']);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYHOST, $this->requestSslCaCert['verifyPeer']);
        if ($this->requestSslCaCert['verifyPeer']) {
            curl_setopt($this->handler, CURLOPT_CAINFO, $this->requestSslCaCert['caCert']);
        }
    }

    /**
     * 构建请求超时
     * @return void
     */
    private function buildRequestTimeout()
    {
        if ($this->requestSecond != 0) {
            curl_setopt($this->handler, CURLOPT_TIMEOUT, $this->requestSecond);
        }
        if ($this->requestMsSecond != 0) {
            curl_setopt($this->handler, CURLOPT_TIMEOUT_MS, $this->requestMsSecond);
        }
    }

    /**
     * 构建请求 cookie
     * @return void
     */
    private function buildCookie()
    {
        if (empty($this->requestCookies)) {
            return;
        }

        $cookie = '';
        foreach ($this->requestCookies as $key => $value) {
            $cookie .= "{$key}={$value};";
        }

        curl_setopt($this->handler, CURLOPT_COOKIE, rtrim($cookie, ';'));
    }

    /**
     * 构建代理请求
     * @return void
     */
    private function buildProxy()
    {
        if (empty($this->proxy)) {
            return;
        }

        curl_setopt($this->handler, CURLOPT_PROXY, $this->proxy['host']);
        curl_setopt($this->handler, CURLOPT_PROXYPORT, $this->proxy['port']);
    }


    /**
     * 构建 header 请求头
     * @return void
     */
    private function buildHeader()
    {
        if (empty($this->requestHeader)) {
            return;
        }

        $header = [];
        foreach ($this->requestHeader as $key => $value) {
            $header[] = "{$key}:{$value}";
        }

        curl_setopt($this->handler, CURLOPT_HTTPHEADER, $header);
    }


    /**
     * @param $method
     * @param $args
     * @return void
     * @throws \Exception
     */
    public static function __callStatic($method, $args)
    {
        switch (ucfirst($method)) {
            case 'Post':
                return (new Post())->do(...$args);
            case 'Get':
                return (new Get())->do(...$args);
            case 'Put':
                return (new Put())->do(...$args);
            case 'Delete':
                return (new Delete())->do(...$args);
            default:
                throw new \Exception("未支持的类型");
        }
    }


//    public function __toString()
//    {
//        // 处理请求日志的格式化输出
//    }


    public function __destruct()
    {
        // 尝试做一些日志的请求
        // TODO: Implement __destruct() method.
    }

}