<?php
namespace tingyu\HttpRequest;

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

    // 请求浏览器标识
    private $requestUserAgent;

    // 请求超时时间
    private $requestSecond;

    // 请求超时时间 毫秒
    private $requestMsSecond;

    // 请求头 array
    private $requestHeader;

    // 是否开启https证书请求
    private $requestSslVerifyPeer;

    // https证书存放路径
    private $requestSslCaCertFile;

    // 响应头信息
    public $responseHeader;

    // 响应结果
    protected $responseBody;

    // 响应cookie信息 数组
    public $responseCookies;

    // 响应的状态码信息
    public $responseStatusCode;

    /**
     * @throws \Exception
     */
    public function __construct()
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
        $this->setUserAgent($userAgent);

        // 设置某人的超时时间
        $this->setTimeOut(30);

        // 设置默认是否校验https证书
        $this->setSslVerifyPeer('');

        // 设置请求结果不直接输出，而是返回数据
        curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);

        // 获取响应头部及响应的cookie
        curl_setopt($this->handler, CURLOPT_HEADERFUNCTION, [$this, 'dealResponseHeader']);
    }


    /**
     * 设置请求携带的cookie值
     * @param string $key
     * @param string $value
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
        $this->buildCookie();
    }


    /**
     * SSL PEM 证书验证
     * @param string $sslCertPath ssl证书绝对路径
     * @param string $sslKeyPath ssl密钥结对路径
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

        curl_setopt($this->handler,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($this->handler,CURLOPT_SSLCERT, $sslCertPath);
        curl_setopt($this->handler,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($this->handler,CURLOPT_SSLKEY, $sslKeyPath);
    }

    /**
     * 设置https请求的时候是否进行证书校验
     * @param string $caCertPemFile  当进行证书校验的时候，应该加载证书的地址，证书可从https://curl.haxx.se/ca/cacert.pem下载
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

        $this->requestSslVerifyPeer = $isVerifyPeer;
        curl_setopt($this->handler,CURLOPT_SSL_VERIFYPEER, $isVerifyPeer);
        curl_setopt($this->handler, CURLOPT_SSL_VERIFYHOST, $isVerifyPeer);
        if ($isVerifyPeer) {
            curl_setopt($this->handler, CURLOPT_CAINFO, $this->requestSslCaCertFile);
        }
    }

    /**
     * 设置请求标识 UA
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
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
    public function setTimeOut($second)
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
    public function setTimeoutMs($msSecond)
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
    public function setProxy($proxyHost, $proxyPort)
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
    public function setHeader($key, $value)
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

    /**
     * 发起请求
     * @param string $uri  请求地址
     * @param string|array|object $data  请求提
     * @return mixed
     */
    abstract function do(string $uri, $data);

    public function __destruct()
    {
        // 尝试做一些日志的请求
        // TODO: Implement __destruct() method.
    }

}