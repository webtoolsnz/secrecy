<?php

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
     * @param string $name
     * @return string
     * @throws SecretNotFoundException
     */
    public function get(string $name): string;

    /**
     * Returns a iterable of key => value pairs
     *
     * @return iterable
     */
    public function list(): iterable;

    /**
     * Attempts to create a new secret
     *
     * @param string $name
     * @param string $value
     * @throws SecretAlreadyExistsException
     */
    public function create(string $name, string $value): void;

    /**
     * Updates an existing secret by name
     *
     * @param string $name
     * @param string $value
     * @throws SecretNotFoundException
     */
    public function update(string $name, string $value): void;

    /**
     * Removes and existing secret
     *
     * @param string $name
     * @throws SecretNotFoundException
     */
    public function remove(string $name): void;
}