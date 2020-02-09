<?php

declare(strict_types=1);

namespace Secrecy\Adapter;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Exception\ValidationException;
use Safe\Exceptions\FilesystemException;
use Safe\Exceptions\JsonException;
use Secrecy\Exception\JsonFileLoadException;
use Secrecy\Exception\JsonFilePersistenceException;
use Secrecy\Exception\SecretAlreadyExistsException;
use Secrecy\Exception\SecretNotFoundException;
use function Safe\file_get_contents;
use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\file_put_contents;
use JsonSchema\Validator;

class JsonFileAdapter implements AdapterInterface
{
    private $path;

    private $data;

    /**
     * Json schema used to validate the secrets file when loading
     *
     * @var array
     */
    private $schema = [
        'type' => 'object',
        'properties' => [
            'secrets' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']]
        ],
        'required' => ['secrets'],
        'additionalProperties' => false,
    ];

    /**
     * JsonFileAdapter constructor.
     * @param string $path
     * @throws JsonFileLoadException
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->load();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name): string
    {
        $this->assertSecretExists($name);
        return (string)$this->data['secrets'][$name];
    }

    /**
     * {@inheritDoc}
     */
    public function list(): iterable
    {
        return $this->data['secrets'];
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonFilePersistenceException
     */
    public function update(string $name, string $value): void
    {
        $this->assertSecretExists($name);
        $this->data['secrets'][$name] = $value;
        $this->persist();
    }

    /**
     * @param string $name
     * @param string $value
     * @throws JsonFilePersistenceException
     * @throws SecretAlreadyExistsException
     */
    public function create(string $name, string $value): void
    {
        $this->assertSecretDoesNotExist($name);
        $this->data['secrets'][$name] = $value;
        $this->persist();
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name): void
    {
        $this->assertSecretExists($name);
        unset($this->data['secrets'][$name]);
        $this->persist();
    }

    /**
     * @throws JsonFileLoadException
     */
    private function load(): void
    {
        try {
            $validator = new Validator();
            $this->data = json_decode(@file_get_contents($this->path), true);
            $validator->validate($this->data, $this->schema,
                Constraint::CHECK_MODE_EXCEPTIONS | Constraint::CHECK_MODE_TYPE_CAST);
        } catch (JsonException | ValidationException | FilesystemException $exception) {
            throw new JsonFileLoadException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Data will be encoded and persisted to filesystem, any issues should trigger an exception
     *
     * @throws JsonFilePersistenceException
     */
    private function persist()
    {
        try {
            @file_put_contents($this->path, json_encode($this->data, JSON_PRETTY_PRINT));
        } catch (FilesystemException | JsonException $exception) {
            throw new JsonFilePersistenceException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @param $name
     * @throws SecretNotFoundException
     */
    private function assertSecretExists($name)
    {
        if (!array_key_exists($name, $this->data['secrets'])) {
            throw SecretNotFoundException::create($name);
        }
    }

    /**
     * @param $name
     * @throws SecretAlreadyExistsException
     */
    private function assertSecretDoesNotExist($name)
    {
        if (array_key_exists($name, $this->data['secrets'])) {
            throw SecretAlreadyExistsException::create($name);
        }
    }
}