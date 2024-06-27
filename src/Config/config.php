<?php

namespace Pp\Api\Config;

use Dotenv\Dotenv;
use Whoops\Handler\JsonResponseHandler as WhoopsJsonResponseHandler;
use Whoops\Run as WhoopsRun;

enum Environment: string
{
  case DEVELOPMENT = "development";
  case PRODUCTION = "production";

  public function envName()
  {
    return match ($this) {
      self::PRODUCTION => "production",
      self::DEVELOPMENT => "development"
    };
  }
}


$whoops = new WhoopsRun();
$whoops->pushHandler(new WhoopsJsonResponseHandler());
$whoops->register();

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

$dotenv->required(["DB_HOST", "DB_NAME", "DB_USER", "DB_PASSWORD"]);