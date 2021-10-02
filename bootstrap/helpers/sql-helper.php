<?php

use Illuminate\Support\Facades\DB;

/**
 * Dumps the SQL with data bindings
 * If $builder is null then get all queries executed in the request.
 *
 * @param  $builder
 * @return void
 */
function dumpSql($builder = null)
{
    $query = exportSqlQuery($builder);
    dump($query);
}

/**
 * @param null $builder
 * @return array|string
 */
function exportSqlQuery($builder = null)
{
    if (!is_null($builder)) {
        $query = str_replace(['?'], ['\'%s\''], $builder->toSql());
        $query = vsprintf($query, $builder->getBindings());

        return $query;
    } else {
        return [
            'primary' => exportSqlConnectionQuery('mysql'),
        ];
    }
}

/**
 * Export SQL that has been executed by the database connection.
 * Including the bindings
 *
 * @param string $connection
 * @return array
 */
function exportSqlConnectionQuery(string $connection): array
{
    $queries = DB::connection($connection)->getQueryLog();
    $q = [];

    foreach ($queries as $key => $query) {
        $q[] = vsprintf(str_replace(['?'], ['\'%s\''], $query['query']), $query['bindings']);
    }

    return $q;
}
