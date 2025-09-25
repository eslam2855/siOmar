<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Reservation;

class ReservationStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $reservation;
    public $oldStatus;
    public $newStatus;
    public $adminNotes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, string $oldStatus, string $newStatus, ?string $adminNotes = null)
    {
        $this->reservation = $reservation;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->adminNotes = $adminNotes;
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
        $statusMessages = [
            'pending' => 'Your reservation has been created and is pending approval.',
            'confirmed' => 'Your reservation has been confirmed! You can now proceed with your stay.',
            'active' => 'Your reservation is now active. Welcome to your stay!',
            'completed' => 'Your reservation has been completed. Thank you for choosing us!',
            'cancelled' => 'Your reservation has been cancelled.',
        ];

        $statusColors = [
            'pending' => '#f59e0b',
            'confirmed' => '#10b981',
            'active' => '#3b82f6',
            'completed' => '#6b7280',
            'cancelled' => '#ef4444',
        ];

        $message = (new MailMessage)
            ->subject("Reservation #{$this->reservation->reservation_number} - Status Updated")
            ->greeting("Hello {$notifiable->name}!")
            ->line($statusMessages[$this->newStatus] ?? "Your reservation status has been updated to {$this->newStatus}.")
            ->line("Reservation Number: {$this->reservation->reservation_number}")
            ->line("Unit: {$this->reservation->unit->name}")
            ->line("Check-in: {$this->reservation->check_in_date->format('M d, Y')}")
            ->line("Check-out: {$this->reservation->check_out_date->format('M d, Y')}")
            ->line("Total Amount: " . config('app.currency_symbol', 'EGP') . number_format($this->reservation->total_amount, 2));

        if ($this->adminNotes) {
            $message->line("Admin Notes: {$this->adminNotes}");
        }

        if ($this->newStatus === 'confirmed') {
            $message->action('View Reservation Details', url('/reservations/' . $this->reservation->id));
        }

        $message->line('Thank you for choosing our service!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'reservation_number' => $this->reservation->reservation_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'admin_notes' => $this->adminNotes,
            'unit_name' => $this->reservation->unit->name,
            'check_in_date' => $this->reservation->check_in_date->format('Y-m-d'),
            'check_out_date' => $this->reservation->check_out_date->format('Y-m-d'),
            'total_amount' => $this->reservation->total_amount,
        ];
    }
}
