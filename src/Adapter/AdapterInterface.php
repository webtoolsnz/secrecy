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

namespace Secrecy\Adapter;

use Secrecy\Exception\SecretAlreadyExistsException;
use Secrecy\Exception\SecretNotFoundException;

interface AdapterInterface
{
    /**
     * Retrieve the secret from persistence layer.
     * If no secret by that name exists SecretNotFoundException will be thrown.
     *
     * @throws SecretNotFoundException
     */
    public function get(string $name): string;

    /**
     * Returns a iterable of key => value pairs.
     */
    public function list(): iterable;

    /**
     * Attempts to create a new secret.
     *
     * @throws SecretAlreadyExistsException
     */
    public function create(string $name, string $value): void;

    /**
     * Updates an existing secret by name.
     *
     * @throws SecretNotFoundException
     */
    public function update(string $name, string $value): void;

    /**
     * Removes and existing secret.
     *
     * @throws SecretNotFoundException
     */
    public function remove(string $name): void;
}
