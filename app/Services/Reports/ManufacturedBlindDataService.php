<?php

namespace App\Services\Reports;

use App\Interfaces\ServiceDataInterface;

class ManufacturedBlindDataService implements ServiceDataInterface
{
    /**
     * @var array
     */
    private $filters;

    /**
     * TeamStatusDataService constructor.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retrieve Manufactured Blinds Data, the return data will largely based on the
     * passed filters array
     *
     * @param string $type
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getData(string $type)
    {

    }

    public function buildQuery()
    {

    }

    public function applyFilters()
    {

    }
}
