<?php

namespace Pp\Api\ORM;

use Exception;
use Pp\Api\Entity\User as UserEntity;
use RedBeanPHP\R;
use RedBeanPHP\RedException\SQL;

class UserModel
{
  const TABLE_NAME = 'users';

  public static function create(UserEntity $data): false|string
  {
    //TODO: Add Exception Handling

    $user_bean = R::dispense(self::TABLE_NAME);
    $user_bean->uuid = $data->getUuid();
    $user_bean->firstname = $data->getFirstname();
    $user_bean->lastname = $data->getLastname();
    $user_bean->email = $data->getEmail();
    $user_bean->password = $data->getPassword();
    $user_bean->phone = $data->getPhone();
    $user_bean->created_at = $data->getCreatedAt();

    try {
      $user_bean_id = R::store($user_bean);
    } catch (SQL $e) {
      return false;
    } finally {
      R::close();
    }

    $user_bean = R::load(self::TABLE_NAME, $user_bean_id);
    return $user_bean->uuid;
  }

  public static function get_all(): false|array
  {
    $user_beans = R::findAll(self::TABLE_NAME);
    $user_exists = $user_beans && count($user_beans);
    if (!$user_exists) {
      return [];
    }

    return array_map(function ($user): array {
      $entity = new UserEntity();
      $entity->unSerialize($user->export());
      return [
        "uuid" => $entity->getUuid(),
        "firstname" => $entity->getFirstname(),
        "lastname" => $entity->getLastname(),
        "email" => $entity->getEmail(),
        "phone" => $entity->getPhone(),
        "created_at" => $entity->getCreatedAt(),
      ];
    }, $user_beans);
  }

  public static function deleteByUuid(string $uuid): bool
  {
    $user = self::getByUuid($uuid);
    try {
      R::trash($user);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public static function getByUuid(string $uuid): ?UserEntity
  {
    $user = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);
    $user_bean_export = $user->export();
    $user_entity = new UserEntity();

    return $user_entity->unSerialize($user_bean_export);
  }

  public static function getByEmail(string $email): ?UserEntity
  {
    $user = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
    $user_bean_export = $user->export();
    $user_entity = new UserEntity();

    return $user_entity->unSerialize($user_bean_export);
  }

  public static function updateByUuid(string $uuid, UserEntity $updated_user): bool|object
  {
    $user_bean = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);

    if ($user_bean) {
      if ($updated_user->getFirstname()) {
        $user_bean->firstname = $updated_user->getFirstname();
      }
      if ($updated_user->getLastname()) {
        $user_bean->lastname = $updated_user->getLastname();
      }
      if ($updated_user->getPhone()) {
        $user_bean->phone = $updated_user->getPhone();
      }

      try {
        $user_bean_id = R::store($user_bean);
      } catch (SQL $e) {
        return false;
      } finally {
        R::close();
      }

      $updated_user_bean = R::findOne(self::TABLE_NAME, 'id = :id', ['id' => $user_bean_id]);
      unset($updated_user_bean->id);
      return $updated_user_bean;
    }
    return false;
  }

  public static function emailExists(string $email): bool
  {
    $user_bean = R::findOne(self::TABLE_NAME, 'email = :email', ['email' => $email]);
    return $user_bean !== null;
  }

  public static function setUserToken(string $jwt_token, string $uuid): void
  {
    $user_bean = R::findOne(self::TABLE_NAME, 'uuid = :uuid', ['uuid' => $uuid]);
    $user_bean->session_token = $jwt_token;
    $user_bean->last_session = time();

    R::store($user_bean);
    R::close();
  }
}