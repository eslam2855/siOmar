<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;

class DepositVerifiedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Deposit Verified - Reservation #{$this->reservation->reservation_number}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Great news! Your deposit payment has been verified.")
            ->line("Reservation Number: {$this->reservation->reservation_number}")
            ->line("Unit: {$this->reservation->unit->name}")
            ->line("Deposit Amount: " . config('app.currency_symbol', 'EGP') . number_format($this->reservation->transfer_amount, 2))
            ->line("Your reservation is now ready for confirmation.")
            ->action('View Reservation Details', url('/reservations/' . $this->reservation->id))
            ->line('Thank you for your payment!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'deposit_amount' => $this->reservation->transfer_amount,
            'unit_name' => $this->reservation->unit->name,
        ];
    }
}
