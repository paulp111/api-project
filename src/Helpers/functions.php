<?php

namespace Pp\Api\Helpers;


/**
 * Returns Data in JSON Format
 * @param mixed $data
 * @return void
 */
function response(mixed $data): void
{
  echo json_encode($data);
}

function hashPassword(string $password): string
{
  return password_hash($password, PASSWORD_ARGON2I);
}

function verifyPassword(string $password, string $hash): bool
{
  return password_verify($password, $hash);
}