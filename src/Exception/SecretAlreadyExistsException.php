<?php

/*
 * This file is part of the secrecy/secrecy package.
 *
 * (c) Webtools Ltd <support@webtools.co.nz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Secrecy\Exception;

class SecretAlreadyExistsException extends \Exception implements ExceptionInterface
{
    public static function create(string $name): self
    {
        return new self("Secret with the name '${name}' already exists");
    }
}
