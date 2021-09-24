<?php

namespace App\Services;

use App\Interfaces\ServiceDataInterface;
use App\Models\Export;
use App\Models\User;

class ExportPayloadService
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Export
     */
    private $export;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var ServiceDataInterface
     */
    private $service;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * ExportPayloadService constructor.
     *
     * @param User $user
     * @param Export $export
     * @param array $headers
     * @param ServiceDataInterface $service
     * @param string $path
     * @param string $name
     * @param string $type
     *
     * @param array $headers
     */
    public function __construct(
        User $user,
        Export $export,
        array $headers,
        ServiceDataInterface $service,
        string $path,
        string $name,
        string $type
    ) {
        $this->user = $user;
        $this->export = $export;
        $this->headers = $headers;
        $this->service = $service;
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
    }

    /*Getters*/
    /**
     * Return the CSV headers
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Return the user
     *
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Return the export instance
     *
     * @return Export
     */
    public function getExport(): Export
    {
        return $this->export;
    }

    /**
     * Return the data to be exported
     *
     * @return ServiceDataInterface
     */
    public function getService(): ServiceDataInterface
    {
        return $this->service;
    }

    /**
     * Return the path where the exported file should be saved
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the name of the file
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the file type
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
