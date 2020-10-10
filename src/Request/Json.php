<?php


namespace tingyu\HttpRequest\Request;

/**
 * json提交
 * Class RequestJson
 * @package tingyu\HttpRequest\Request
 */
class Json implements IRequest
{
    private $contentType = 'application/json';

    private $data;
    
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 设置 请求体
     * @param $data
     * @throws \Exception
     */
    public function setData($data)
    {
        if (!is_string($data)) {
            throw new \Exception("参数类型不合法");
        }

        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
}