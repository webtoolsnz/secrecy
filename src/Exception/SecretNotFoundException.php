<?php

namespace Secrecy\Exception;

class SecretNotFoundException extends \Exception implements ExceptionInterface
{
    public static function create($name)
    {
        return new self("Secret with the name '${name}' was not found");
    }
}