<?php


namespace tingyu\HttpRequest\Request;

/**
 * xml 提交
 * Class RequestXml
 * @package tingyu\HttpRequest\Request
 */
class Xml implements IRequest
{
    private $contentType = 'application/xml';

    private $data;

    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 设置请求体
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