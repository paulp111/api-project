<?php

use Pp\Api\Config\AllowCors;

(new AllowCors())->init();
header("Content-Type: application/json");