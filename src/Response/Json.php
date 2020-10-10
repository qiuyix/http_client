<?php


namespace tingyu\HttpRequest\Response;


class Json implements IResponse
{
    /**
     * 解析 json
     * @param $result
     * @return array
     * @throws \Exception
     */
    public function parse($result)
    {
        if (!$result) {
            return "json数据异常";
        }

        $data = json_decode($result, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('json数据异常');
        }

        return $data;
    }


}