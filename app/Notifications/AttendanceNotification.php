<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendanceNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $attendance;
    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct(Attendance $attendance, string $type = 'late')
    {
        $this->attendance = $attendance;
        $this->type = $type; // 'late', 'absent', 'failed'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'late' => 'Notifikasi Keterlambatan Presensi',
            'absent' => 'Notifikasi Tidak Hadir',
            'failed' => 'Notifikasi Presensi Gagal',
            default => 'Notifikasi Presensi'
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
            markdown: 'emails.attendance.notification',
            with: [
                'attendance' => $this->attendance,
                'type' => $this->type,
                'employee' => $this->attendance->user->employee,
                'user' => $this->attendance->user,
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
