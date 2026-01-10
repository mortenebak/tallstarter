<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public TeamInvitation $invitation
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        $acceptUrl = route('teams.invitations.accept', [
            'invitation' => $this->invitation->id,
            'token' => $this->invitation->token,
        ], absolute: true);

        return (new MailMessage)
            ->subject(__('teams.invitation_subject', ['team' => $this->invitation->team->name]))
            ->line(__('teams.invitation_line_1', ['team' => $this->invitation->team->name]))
            ->line(__('teams.invitation_line_2', ['role' => $this->invitation->role]))
            ->action(__('teams.accept_invitation'), $acceptUrl)
            ->line(__('teams.invitation_line_3'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->invitation->team_id,
            'team_name' => $this->invitation->team->name,
            'role' => $this->invitation->role,
        ];
    }
}
