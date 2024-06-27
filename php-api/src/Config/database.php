<?php

namespace Pp\Api\Config;

use RedBeanPHP\R;

R::setup('mysql:host=localhost;dbname=api',
  $_ENV["DB_USER"], $_ENV["DB_PASSWORD"]);


$currentEnv = Environment::tryFrom($_ENV["APP_ENV"]);
if ($currentEnv?->envName() !== Environment::DEVELOPMENT->value) {
  R::freeze(true);
}