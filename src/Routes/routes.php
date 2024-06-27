<?php

namespace Pp\Api\Routes;

use Pp\Api\Routes\Exception\NotAllowedException;
use Pp\Api\Service\Exception\CanNotLoginUserException;
use Pp\Api\Service\Exception\EmailExistsException;
use Pp\Api\Validation\Exception\ValidationException;
use PH7\JustHttp\StatusCode;
use function Pp\Api\Helpers\response;

$resource = $_REQUEST["resource"] ?? null;

try {
  return match ($resource) {
    "user" => require "user.routes.php",
    default => require "404.routes.php"
  };
} catch (ValidationException|NotAllowedException $e) {
  \PH7\PhpHttpResponseHeader\Http::setHeadersByCode(StatusCode::BAD_REQUEST);
  response([
    "errors" => [
      "message" => $e->getMessage(),
      "code" => $e->getCode()
    ]
  ]);
} catch (EmailExistsException|CanNotLoginUserException $e) {
  response([
    "errors" => [
      "message" => $e->getMessage()
    ]
  ]);
}

