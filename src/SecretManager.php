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

namespace Secrecy;

use Secrecy\Adapter\AdapterInterface;

class SecretManager
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Retrieve a secret with the given name.
     *
     * @param $name
     *
     * @throws Exception\SecretNotFoundException
     */
    public function get($name): string
    {
        return $this->adapter->get($name);
    }

    /**
     * Returns an iterable consisting of key => value pairs.
     */
    public function list(): iterable
    {
        return $this->adapter->list();
    }

    /**
     * Create and persist an new secret.
     *
     * @throws Exception\SecretAlreadyExistsException
     */
    public function create(string $name, string $value): void
    {
        $this->adapter->create($name, $value);
    }

    /**
     * Update an existing secret.
     *
     * @throws Exception\SecretNotFoundException
     */
    public function update(string $name, string $value): void
    {
        $this->adapter->update($name, $value);
    }

    /**
     * Remove a secret with the given name.
     *
     * @throws Exception\SecretNotFoundException
     */
    public function remove(string $name): void
    {
        $this->adapter->remove($name);
    }
}
