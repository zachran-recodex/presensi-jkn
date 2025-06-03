<?php

namespace App\Notifications;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceAnomalyDetected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $attendance;
    protected $anomalyType;
    protected $anomalyDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct(Attendance $attendance, string $anomalyType, array $anomalyDetails = [])
    {
        $this->attendance = $attendance;
        $this->anomalyType = $anomalyType;
        $this->anomalyDetails = $anomalyDetails;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $user = $this->attendance->user;
        $employee = $user->employee;

        $message = (new MailMessage)
            ->subject('Alert: Anomali Presensi Terdeteksi')
            ->greeting('Halo Admin,')
            ->line('Sistem telah mendeteksi anomali pada presensi karyawan berikut:');

        // Add employee details
        $message->line('**Detail Karyawan:**')
            ->line('- Nama: ' . $user->name)
            ->line('- ID Karyawan: ' . ($employee->employee_id ?? '-'))
            ->line('- Departemen: ' . ($employee->department ?? '-'));

        // Add attendance details
        $message->line('**Detail Presensi:**')
            ->line('- Tanggal: ' . $this->attendance->attendance_date->format('d F Y'))
            ->line('- Waktu: ' . $this->attendance->attendance_time->format('H:i:s'))
            ->line('- Jenis: ' . ($this->attendance->type === 'clock_in' ? 'Masuk' : 'Pulang'))
            ->line('- Status: ' . $this->attendance->status);

        // Add anomaly-specific details
        switch ($this->anomalyType) {
            case 'low_face_similarity':
                $message->line('**Jenis Anomali:** Skor Kemiripan Wajah Rendah')
                    ->line('- Skor Kemiripan: ' . number_format($this->attendance->face_similarity_score * 100, 2) . '%')
                    ->line('- Ambang Batas: ' . number_format(config('services.biznet_face.similarity_threshold', 0.75) * 100, 2) . '%');
                break;

            case 'location_anomaly':
                $message->line('**Jenis Anomali:** Lokasi Presensi Tidak Valid')
                    ->line('- Jarak dari Kantor: ' . number_format($this->attendance->distance_from_office, 0) . ' meter')
                    ->line('- Batas Maksimal: ' . ($this->attendance->location->radius ?? 'N/A') . ' meter');
                break;

            case 'time_anomaly':
                $message->line('**Jenis Anomali:** Waktu Presensi Tidak Wajar')
                    ->line('- Jam Kerja Normal: ' . ($employee->work_start_time ?? 'N/A') . ' - ' . ($employee->work_end_time ?? 'N/A'));
                break;

            case 'repeated_failures':
                $message->line('**Jenis Anomali:** Gagal Presensi Berulang')
                    ->line('- Jumlah Kegagalan: ' . ($this->anomalyDetails['failure_count'] ?? 'N/A'))
                    ->line('- Periode: ' . ($this->anomalyDetails['period'] ?? 'N/A'));
                break;

            default:
                $message->line('**Jenis Anomali:** ' . ucfirst(str_replace('_', ' ', $this->anomalyType)));
        }

        $message->line('Silakan periksa dan tindak lanjuti jika diperlukan.')
            ->action('Lihat Detail Presensi', url('/admin/attendance-history?employee=' . urlencode($user->name)))
            ->line('Email ini dikirim otomatis oleh sistem presensi.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance_anomaly',
            'title' => 'Anomali Presensi Terdeteksi',
            'message' => $this->getAnomalyMessage(),
            'attendance_id' => $this->attendance->id,
            'user_id' => $this->attendance->user_id,
            'user_name' => $this->attendance->user->name,
            'employee_id' => $this->attendance->user->employee->employee_id ?? null,
            'anomaly_type' => $this->anomalyType,
            'anomaly_details' => $this->anomalyDetails,
            'attendance_date' => $this->attendance->attendance_date,
            'attendance_time' => $this->attendance->attendance_time,
            'detected_at' => now(),
            'action_url' => url('/admin/attendance-history?employee=' . urlencode($this->attendance->user->name))
        ];
    }

    /**
     * Get anomaly message based on type
     */
    private function getAnomalyMessage(): string
    {
        $userName = $this->attendance->user->name;

        return match($this->anomalyType) {
            'low_face_similarity' => "Skor kemiripan wajah rendah untuk {$userName}: " . number_format($this->attendance->face_similarity_score * 100, 2) . '%',
            'location_anomaly' => "Presensi {$userName} di luar lokasi yang valid (jarak: " . number_format($this->attendance->distance_from_office, 0) . 'm)',
            'time_anomaly' => "Waktu presensi {$userName} di luar jam kerja normal",
            'repeated_failures' => "Kegagalan presensi berulang untuk {$userName}",
            default => "Anomali presensi terdeteksi untuk {$userName}"
        };
    }
}
