<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SbgModel extends Model
{
    /**
     * Generate a case when query that will create a new computed column based on the options given.
     * The `options` parameter should be a key value pair, where the key will be use for comparison and the
     * value will be used as the column value.
     *
     * 1 => 'Success'
     *
     * @param array $options
     * @param string $column
     * @param string $alias
     *
     * @return string
     */
    protected function queryCaseColumn(array $options, string $column, string $alias): string
    {
        $caseQuery = "CASE";

        foreach ($options as $key => $option) {
            $caseQuery .= "\t WHEN {$column} = {$key} THEN '${option}'";
        }

        $caseQuery .= " END as {$alias}";

        return $caseQuery;
    }
}
