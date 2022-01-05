<?php

namespace App\Notifications\StockOrder;

use App\Models\StockOrder\StockOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPickingListNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @var StockOrder
     */
    private $stockOrder;

    /**
     * @var string
     */
    private $url;

    /**
     * Create a new notification instance.
     *
     * @param StockOrder $stockOrder
     * @param string $url
     *
     * @return void
     */
    public function __construct(StockOrder $stockOrder, string $url)
    {
        $this->stockOrder = $stockOrder;
        $this->url = $url;
    }

    /**
     * @return string[]
     */
    public function tags()
    {
        return [
            'order-picking',
            'order-picking-list-notification',
        ];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->stockOrder->sage_order_no} Order Picking")
            ->greeting("Order Picking for {$this->stockOrder->sage_order_no}")
            ->line("Here's the order picking PDF for the order {$this->stockOrder->sage_order_no}. See attached file.")
            ->attach($this->url)
            ->salutation(" ");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
