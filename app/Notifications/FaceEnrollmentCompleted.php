<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FaceEnrollmentCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    protected $enrolledUser;
    protected $enrolledBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $enrolledUser, User $enrolledBy)
    {
        $this->enrolledUser = $enrolledUser;
        $this->enrolledBy = $enrolledBy;
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
        return (new MailMessage)
            ->subject('Pendaftaran Wajah Berhasil - Sistem Presensi')
            ->greeting('Halo ' . $this->enrolledUser->name . ',')
            ->line('Pendaftaran wajah Anda untuk sistem presensi telah berhasil dilakukan.')
            ->line('Sekarang Anda dapat melakukan presensi menggunakan fitur face recognition.')
            ->line('Detail pendaftaran:')
            ->line('- Nama: ' . $this->enrolledUser->name)
            ->line('- Email: ' . $this->enrolledUser->email)
            ->line('- ID Karyawan: ' . ($this->enrolledUser->employee->employee_id ?? '-'))
            ->line('- Didaftarkan oleh: ' . $this->enrolledBy->name)
            ->line('- Tanggal: ' . now()->format('d F Y H:i'))
            ->action('Login ke Sistem Presensi', url('/dashboard'))
            ->line('Jika Anda memiliki pertanyaan, silakan hubungi admin atau HRD.')
            ->salutation('Terima kasih, Tim ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'face_enrollment_completed',
            'title' => 'Pendaftaran Wajah Berhasil',
            'message' => 'Wajah Anda telah didaftarkan dalam sistem presensi. Sekarang Anda dapat melakukan presensi dengan face recognition.',
            'enrolled_user_id' => $this->enrolledUser->id,
            'enrolled_user_name' => $this->enrolledUser->name,
            'enrolled_by_id' => $this->enrolledBy->id,
            'enrolled_by_name' => $this->enrolledBy->name,
            'enrolled_at' => now(),
            'action_url' => url('/dashboard')
        ];
    }
}
