<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

#[MongoDB\Document]
class ConnectionLog
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    private $userId;

    #[MongoDB\Field(type: "string")]
    private $username;

    #[MongoDB\Field(type: "string")]
    private $ip;

    #[MongoDB\Field(type: "bool")]
    private $success;

    #[MongoDB\Field(type: "date")]
    private $timestamp;

    // ----- GETTERS -----

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function isSuccess(): ?bool
    {
        return $this->success;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    // ----- SETTERS -----

    public function setUserId(string $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;
        return $this;
    }

    public function setTimestamp(\DateTimeInterface $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
