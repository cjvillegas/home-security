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
     * Build the base query that the service class will utilize
     *
     * @return self
     */
    public function buildQuery();

    /**
     * Add more condition based on the provided filtering data.
     *
     * @return self
     */
    public function applyFilters();
}
