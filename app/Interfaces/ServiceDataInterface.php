<?php

namespace App\Interfaces;

interface ServiceDataInterface
{
    /**
     * Retrieve the data
     *
     * @param string $type
     * @return mixed
     */
    public function getData(string $type);

    /**
     * @return self
     */
    public function buildQuery();
}
