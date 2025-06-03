<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FaceEnrollmentNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $type;
    public $enrolledBy;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $type = 'enrolled', User $enrolledBy = null)
    {
        $this->user = $user;
        $this->type = $type; // 'enrolled', 'reenrolled', 'deleted'
        $this->enrolledBy = $enrolledBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'enrolled' => 'Wajah Berhasil Didaftarkan - Sistem Presensi',
            'reenrolled' => 'Wajah Berhasil Didaftarkan Ulang - Sistem Presensi',
            'deleted' => 'Data Wajah Dihapus - Sistem Presensi',
            default => 'Notifikasi Face Recognition'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.face-enrollment.notification',
            with: [
                'user' => $this->user,
                'type' => $this->type,
                'employee' => $this->user->employee,
                'enrolledBy' => $this->enrolledBy,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
