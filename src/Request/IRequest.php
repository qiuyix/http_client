<?php
namespace tingyu\HttpRequest\Request;

interface IRequest
{
    function getContentType();

    function setData($data);

    function getData();
}