<?php

namespace Pp\Api\Routes;

class Http
{
  public const GET_METHOD = 'GET';
  public const POST_METHOD = 'POST';
  public const DELETE_METHOD = 'DELETE';
  public const PUT_METHOD = 'PUT';


  public static function matchHttpRequestMethod(string $http_method): bool
  {
    return strtolower($_SERVER['REQUEST_METHOD']) === strtolower($http_method);
  }
}