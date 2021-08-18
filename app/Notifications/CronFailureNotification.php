<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsChannel;
use NotificationChannels\MicrosoftTeams\MicrosoftTeamsMessage;

class CronFailureNotification extends Notification
{
    use Queueable;

    /**
     * @var string
     */
    private $cron = null;

    /**
     * @var string|null
     */
    private $errorMessage;

    /**
     * Create a new notification instance.
     *
     * @param string $cron
     *
     * @return void
     */
    public function __construct(string $cron, $errorMessage = null)
    {
        $this->cron = $cron;
        $this->errorMessage = $errorMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', MicrosoftTeamsChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                ->error()
                ->greeting('CRON Job Failure!')
                ->line(new HtmlString("An error occurred while running the <b>{$this->cron}</b> CRON. Please report this to your administrator."))
                ->action('View Full Error Log', $this->getUrl())
                ->line(new HtmlString("Click the button and view the full error log. Make sure you are authenticated before you can access the page."))
                ->salutation(new HtmlString("Regards, <br>Dev Team"));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'error',
            'from' => 'cron',
            'cron' => $this->cron,
            'message' => 'Cron Job Failure',
            'error_message' => $this->errorMessage,
            'date' => now()->format('Y-m-d H:i')
        ];
    }

    /**
     * Get the teams representation of the notification
     *
     * @param mixed $notifiable
     *
     * @return MicrosoftTeamsMessage
     */
    public function toMicrosoftTeams($notifiable)
    {
        return MicrosoftTeamsMessage::create()
            ->to(config('services.teams.dev_cron_failure'))
            ->type('error')
            ->title("{$this->cron} Failed")
            ->content(new HtmlString("An error occurred while running the <b>{$this->cron}</b> CRON. Please report this to your administrator."))
            ->button('View Full Error Log', $this->getUrl());
    }

    /**
     * Return the URL of the notification
     *
     * @return string
     */
    private function getUrl()
    {
        return config('app.url') . '/admin/notifications#/notification-index/' . $this->id;
    }
}
