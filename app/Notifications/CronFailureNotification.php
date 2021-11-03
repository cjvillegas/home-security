<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

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
        $this->environment = strtoupper(env('APP_ENV'));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
                ->line(new HtmlString("{$this->environment}: An error occurred while running the <b>{$this->cron}</b> CRON. Please report this to your administrator."))
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
     * Return the URL of the notification
     *
     * @return string
     */
    private function getUrl()
    {
        return config('app.url') . '/admin/notifications#/notification-index/' . $this->id;
    }
}
