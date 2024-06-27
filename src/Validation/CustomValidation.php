<?php

namespace Pp\Api\Validation;

use Respect\Validation\Validator as v;

class CustomValidation
{
  const MAX_STRING = 50;
  const MIN_STRING = 3;
  const MIN_PASSWORD = 8;

  public function __construct(private readonly mixed $data)
  {
  }

  public function validate_create(): bool
  {
    $validation = v::attribute("firstname", v::stringType()->length(self::MIN_STRING, self::MAX_STRING))
      ->attribute("lastname", v::stringType()->length(self::MIN_STRING, self::MAX_STRING))
      ->attribute("email", v::email())
      ->attribute("password", v::stringType()->length(self::MIN_PASSWORD))
      ->attribute("phone", v::phone(), false);

    return $validation->validate($this->data);
  }

  public function validate_update(): bool
  {
    $validation = v::attribute("uuid", v::uuid(4))
      ->attribute("firstname", v::stringType()->length(self::MIN_STRING, self::MAX_STRING))
      ->attribute("lastname", v::stringType()->length(self::MIN_STRING, self::MAX_STRING))
      ->attribute("phone", v::phone(), false);

    return $validation->validate($this->data);
  }

  public function validate_uuid(): bool
  {
    return v::uuid(4)->validate($this->data);
  }

  public function validate_login(): bool
  {
    $validation = v::attribute("email", v::email())
      ->attribute("password", v::stringType()->length(self::MIN_PASSWORD));

    return $validation->validate($this->data);
  }
}