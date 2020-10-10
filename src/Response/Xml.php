<?php


namespace tingyu\HttpRequest\Response;


class Xml implements IResponse
{
    /**
     * 解析 xml
     * @param $result
     * @return array
     * @throws \Exception
     */
    public function parse($result)
    {
        if (!$result) {
            throw new \Exception('xml数据异常');
        }
        
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}