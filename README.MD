# HttpRequest : make Http Request easy

### Description
curl 请求库，包括常用的请求方式，请求头设置（请求超时设置，cookie设置，请求证书校验设置，ua设置等），请求参数设置（表单上传，文件上传，json，xml等数据格式），获取响应头信息（整个头部信息，cookie信息，响应状态码），响应结果解析（xml,json 转成 php数组）

### Installaction

使用composer安装
```
$ composer require tingyu\HttpRequest
```
或者，在composer.json中加入

### Requirements

HttpRequest work witch php 5.4, 5.5, 5.6, 5.7, 7.0, 7.1, 7.2, 7.3, 7.4 and need ext-curl extersion

### Quick Start and Examples

GET
> ```PHP
> use tingyu\HttpRequest\Method\Get
> 
> $handle = new Get();
> 
> /**
>  * 发起请求并获取响应结果
>  * @param string 请求地址
>  * @param string|array 请求地址参数
>  */
> $responseBody = $handle->request("localhost", ["wd" => "php"]);
>```
POST 并示例发送表单数据
> ```PHP
> use tingyu\HttpRequest\Method\Post;
> use tingyu\HttpRequest\Request\Form;
> 
> /**
>  * 准备表单数据
>  * @para array 表单数据，支持多次赋值，重复键值将覆盖调
>  */
> $form = new Form();
> $form->setData(['language'=>'php']);
> $form->setData(['database'=>'mysql']);
> 
> $handle = new Post($form);
> 
> /**
>  * 发起请求并获取响应结果
>  * @param string 请求地址
>  * @param string|array 请求地址参数
>  */
> $responseBody = $handle->request("localhost");
>```

PUT 并示例发送json数据
> ```PHP
> use tingyu\HttpRequest\Method\Put;
> use tingyu\HttpRequest\Request\Json;
> 
> /**
>  * 准备表单数据
>  * @para array 表单数据，支持多次赋值，重复键值将覆盖调
>  */
> $form = new Json();
> $form->setData('{"msg":"success", "code":200}');
> 
> $handle = new Put($json);
> 
> /**
>  * 发起请求并获取响应结果
>  * @param string 请求地址
>  * @param string|array 请求地址参数
>  */
> $responseBody = $handle->request("localhost");
>```
DELETE 并发送xml数据
> ```PHP
> use tingyu\HttpRequest\Method\Delete;
> use tingyu\HttpRequest\Request\Xml;
> 
> /**
>  * 准备表单数据
>  * @para array 表单数据，支持多次赋值，重复键值将覆盖调
>  */
> $xml = new Xml();
> $form->setData('<xml><language>php</language><database>mysql</database></xml>');
> 
> $handle = new Delete($xml);
> 
> /**
>  * 发起请求并获取响应结果
>  * @param string 请求地址
>  * @param string|array 请求地址参数
>  */
> $responseBody = $handle->request("localhost);
>```
PATCH
> ```PHP
> use tingyu\HttpRequest\Method\Patch;
> 
> $handle = new Patch();
> 
> /**
>  * 发起请求并获取响应结果
>  * @param string 请求地址
>  * @param string|array 请求地址参数
>  */
> $responseBody = $handle->request("http://baidu.com"); 
>```

传输文件
>```php
> // 传输文件需要在表单里面上传
> use tingyu\HttpRequest\Request\Form;
> ...
> $form = new Form();
> $form->setData(...)  // 上传其它的一些表单数据
> /**
>  * $fieldName 表单名
>  * $filePath 上传的文件路径，推荐绝对路径
>  * $fileName 文件名 可选
>  */
> $form->setUploadFile($fieldName, $filePath, $fileName);
>```

更多设置可以参考源码目录下的 `example` 目录下的相关示例

### Security
If you discover any security related issues, please contact us at qiuyix@sina.cn.

### License
The MIT License (MIT). Please see License File for more information.