<?php

namespace App\Abstracts;

use App\Models\User;
use App\Notifications\CronFailureNotification;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

abstract class CronDatabasePopulator extends Command
{
    /**
     * @var string|null
     */
    protected $table;

    /**
     * This will clear the data from the **orders** table.
     * This method will really do truncation on the orders table, not soft deletion.
     *
     * @return bool
     */
    protected function clearTable()
    {
        // sanity checks, if a table is defined
        if (empty($this->table)) {
            return false;
        }

        DB::connection('mysql')
            ->table($this->table)
            ->truncate();

        return true;
    }

    /**
     * Send email and MS Teams notifications to admins when a CRON fails
     *
     * @param string $message
     * @param Exception $error
     *
     * @return void
     */
    protected function sendFailedNotification(string $message, Exception $error):void
    {
        $users = (new User)->getUserAdminsWithValidEmails();

        Notification::send($users, new CronFailureNotification($message, $error->getMessage()));
    }

    /**
     * Get the needed data from the BlindData database
     *
     * @return Collection
     */
    abstract protected function getDataFromBlind(): Collection;

    /**
     * Sanitize the data coming from the BlindData database.
     * This will ensure that we will only be saving item
     * with right information in them
     *
     * @param array $item
     *
     * @return array|boolean
     */
    abstract protected function sanitize(array $item): ?array;
}
