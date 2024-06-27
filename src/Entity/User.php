<?php

namespace Pp\Api\Entity;

class User
{
  private string $uuid;
  private ?string $firstname = null;
  private ?string $lastname = null;
  private string $email;
  private string $password;
  private ?string $phone = null;
  private string $created_at;

  public function getUuid(): string
  {
    return $this->uuid;
  }

  public function setUuid(string $uuid): User
  {
    $this->uuid = $uuid;
    return $this;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): User
  {
    $this->firstname = $firstname;
    return $this;
  }

  public function getLastname(): ?string
  {
    return $this->lastname;
  }

  public function setLastname(string $lastname): User
  {
    $this->lastname = $lastname;
    return $this;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): User
  {
    $this->email = $email;
    return $this;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function setPassword(string $password): User
  {
    $this->password = $password;
    return $this;
  }

  public function getPhone(): ?string
  {
    return $this->phone;
  }

  public function setPhone(string $phone): User
  {
    $this->phone = $phone;
    return $this;
  }

  public function getCreatedAt(): string
  {
    return $this->created_at;
  }

  public function setCreatedAt(string $created_at): User
  {
    $this->created_at = $created_at;
    return $this;
  }

  public function unSerialize(array $user): User
  {
    if (!empty($user["uuid"])) {
      $this->setUuid($user["uuid"]);
    }
    if (!empty($user["firstname"])) {
      $this->setFirstname($user["firstname"]);
    }
    if (!empty($user["lastname"])) {
      $this->setLastname($user["lastname"]);
    }
    if (!empty($user["email"])) {
      $this->setEmail($user["email"]);
    }
    if (!empty($user["password"])) {
      $this->setPassword($user["password"]);
    }
    if (!empty($user["phone"])) {
      $this->setPhone($user["phone"]);
    }
    if (!empty($user["created_at"])) {
      $this->setCreatedAt($user["created_at"]);
    }

    return $this;
  }

  public function serialize(): array
  {
    return get_object_vars($this);
  }

}