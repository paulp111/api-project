<?php

namespace Ls\Api\Service;

use Firebase\JWT\JWT;
use Ls\Api\Entity\User as UserEntity;
use Ls\Api\ORM\UserModel;
use Ls\Api\Service\Exception\CanNotLoginUserException;
use Ls\Api\Service\Exception\EmailExistsException;
use Ls\Api\Validation\CustomValidation;
use Ls\Api\Validation\Exception\ValidationException;
use PH7\JustHttp\StatusCode;
use PH7\PhpHttpResponseHeader\Http;
use Ramsey\Uuid\Uuid;
use RedBeanPHP\RedException\SQL;
use function Ls\Api\Helpers\hashPassword;

class User
{
  public function __construct()
  {
  }

  public function create(mixed $data): array|object
  {
    $validator = new CustomValidation($data);
    if ($validator->validate_create()) {
      $uuid = Uuid::uuid4()->toString();
      $user_entity = new UserEntity();
      $user_entity->setUuid($uuid)
        ->setFirstname($data->firstname)
        ->setLastname($data->lastname)
        ->setEmail($data->email)
        ->setPassword(hashPassword($data->password))
        ->setPhone($data->phone)
        ->setCreatedAt(date("Y-m-d H:i:s"));

      if (UserModel::emailExists($user_entity->getEmail())) {
        $email = $user_entity->getEmail();
        throw new EmailExistsException("Email $email already exists");
      }
      $valid = $user_uuid = UserModel::create($user_entity);
      if (!$valid) {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);
        return [];
      }
      $data->uuid = $user_uuid;
      //TODO: Return data without password
      return $data;

    }
    throw new ValidationException("Validation failed, wrong input data");

  }

  public function get(string $user_id): array|object
  {
    $validation = new CustomValidation($user_id);
    if ($validation->validate_uuid()) {
      if ($user_bean = UserModel::getByUuid($user_id)) {
        return $user_bean->serialize();
      }
      HTTP::setHeadersByCode(StatusCode::NOT_FOUND);
      return [];
    }
    throw new ValidationException("Validation failed, uuid not valid");
  }

  public function getAll(): array|object
  {
    return UserModel::get_all();
  }

  public function update(mixed $user_data): array|object
  {
    $validation = new CustomValidation($user_data);

    if ($validation->validate_update()) {

      $user_uuid = $user_data->uuid;
      $user_entity = new UserEntity();
      if (!empty($user_data->firstname)) {
        $user_entity->setFirstname($user_data->firstname);
      }
      if (!empty($user_data->lastname)) {
        $user_entity->setLastname($user_data->lastname);
      }
      if (!empty($user_data->phone)) {
        $user_entity->setPhone($user_data->phone);
      }

      $valid = $updated_user = UserModel::updateByUuid($user_uuid, $user_entity);
      if (!$valid) {
        Http::setHeadersByCode(StatusCode::NOT_FOUND);
        return [];
      }
      return $updated_user;
    }
    throw new ValidationException("Validation failed, wrong input data");
  }

  public function remove(string $user_id): array|object
  {
    $validation = new CustomValidation($user_id);
    if ($validation->validate_uuid()) {
      $valid = $delete_user = UserModel::deleteByUuid($user_id);
      if (!$valid) {
        Http::setHeadersByCode(StatusCode::NOT_FOUND);
        return ["error" => "User not found"];
      }
      return ["data" => $delete_user];
    }
    throw new ValidationException("Validation failed, uuid not valid");
  }

  public function login(mixed $user_data)
  {
    $validation = new CustomValidation($user_data);
    if ($validation->validate_login()) {
      if (UserModel::emailExists($user_data->email)) {
        $user = UserModel::getByEmail($user_data->email);
        $is_login_valid = $user->getEmail() && password_verify($user_data->password, $user->getPassword());
        if ($is_login_valid) {
          $user_name = $user->getFirstname() . " " . $user->getLastname();
          $current_time = time();
          $payload = [
            "iss" => $_ENV["API_URL"],
            "iat" => $current_time,
            "exp" => $current_time + $_ENV["JWT_TOKEN_EXP"],
            "data" => [
              "email" => $user->getEmail(),
              "user" => $user_name
            ]
          ];
          $jwt_token = JWT::encode($payload, "this is very secret", "HS256");
        }
        try {
          UserModel::setUserToken($jwt_token, $user->getUuid());
        } catch (SQL $e) {
          throw new CanNotLoginUserException("can not set session token");
        }
        return [
          "message" => "$user_name successfully logged in",
          "token" => $jwt_token];
      }
    }
    throw new ValidationException("Validation failed, incorrect email or password");
  }

}