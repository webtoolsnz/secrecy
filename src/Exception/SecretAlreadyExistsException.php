<?php

namespace Secrecy\Exception;

class SecretAlreadyExistsException extends \Exception implements ExceptionInterface
{
    public static function create($name)
    {
        return new self("Secret with the name '${name}' already exists");
    }
}