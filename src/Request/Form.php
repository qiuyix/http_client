<?php


namespace tingyu\HttpRequest\Request;

/**
 * 表单提交，支持文件上传
 * Class RequestFormData
 * @package tingyu\HttpRequest\Request
 */
class Form implements IRequest
{
    private $contentType = 'multipart/form-data';

    private $data = [];

    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 设置form 表单数据
     * @param $data
     * @throws \Exception
     */
    public function setData($data)
    {
        if (!is_array($data)) {
            throw new \Exception('参数类型错误，应为数组类型');
        }

        $this->data = array_merge($this->data, $data);
    }

    /**
     * 上传文件，可多次复制，重复的 field 值将覆盖调
     * @param string $field 字段名
     * @param string $file 文件路径，推荐绝对路径
     * @param string $fileName 文件名
     * @param string $mimeType 文件mime_type
     * @throws \Exception
     */
    public function setUploadFile($field, $file, $fileName = '', $mimeType = '')
    {
        if ($field == '' || $file == '') {
            throw new \Exception('file和filed必传');
        }

        if (!is_string($field)) {
            throw new \Exception("field 值应为字符串");
        }

        if (!file_exists($file)) {
            throw new \Exception("file not fount");
        }

        if (!is_string($fileName) || $fileName == '') {
            $info = pathinfo($file);
            $fileName = $info['basename'];
        }

        $this->data[$field] = new \CURLFile($file, $mimeType, $fileName);
    }

    public function getData()
    {
        return $this->data;
    }
}