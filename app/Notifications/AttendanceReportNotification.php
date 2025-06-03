<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class AttendanceReportNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reportType;
    public $period;
    public $filePath;
    public $summary;

    /**
     * Create a new message instance.
     */
    public function __construct(string $reportType, Carbon $period, string $filePath = null, array $summary = [])
    {
        $this->reportType = $reportType; // 'daily', 'weekly', 'monthly'
        $this->period = $period;
        $this->filePath = $filePath;
        $this->summary = $summary;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->reportType) {
            'daily' => 'Laporan Presensi Harian - ' . $this->period->format('d F Y'),
            'weekly' => 'Laporan Presensi Mingguan - Week ' . $this->period->weekOfYear . ' ' . $this->period->year,
            'monthly' => 'Laporan Presensi Bulanan - ' . $this->period->format('F Y'),
            default => 'Laporan Presensi'
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
            markdown: 'emails.reports.attendance',
            with: [
                'reportType' => $this->reportType,
                'period' => $this->period,
                'summary' => $this->summary,
                'hasAttachment' => !empty($this->filePath),
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
        if (!$this->filePath || !file_exists($this->filePath)) {
            return [];
        }

        $filename = 'laporan_presensi_' . $this->reportType . '_' . $this->period->format('Y_m_d') . '.csv';

        return [
            Attachment::fromPath($this->filePath)
                ->as($filename)
                ->withMime('text/csv'),
        ];
    }
}
