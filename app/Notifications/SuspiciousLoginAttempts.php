<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a user when their account hits a configurable failure threshold
 * (default: 5 consecutive failed login attempts within the throttle window).
 *
 * Goal: alert the legitimate owner that someone is trying to guess their
 * password — they can change it preemptively or enable 2FA if not already.
 *
 * KVKK note: this notification deliberately omits the attempting IP and
 * user-agent. We tell the user "someone tried" without forwarding identifying
 * data about the attacker; the full record is in the audit log for the admin.
 */
class SuspiciousLoginAttempts extends Notification
{
    use Queueable;

    public function __construct(
        public readonly int $attemptCount,
        public readonly bool $accountLocked = false,
    ) {}

    /** @return array<int, string> */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $msg = (new MailMessage)
            ->subject(__('Hesabına girmeye çalışan biri var'))
            ->greeting(__('Merhaba :name,', ['name' => $notifiable->display_name ?? $notifiable->name]))
            ->line(__(
                'Hesabına son dakikalarda :count başarısız giriş denemesi yapıldı.',
                ['count' => $this->attemptCount]
            ));

        if ($this->accountLocked) {
            $msg->line(__('Güvenlik için hesabın geçici olarak kilitlendi. Bir süre sonra tekrar dene.'));
        } else {
            $msg->line(__('Henüz aksiyon almana gerek yok — sınırı aşan denemelerde hesabın otomatik kilitlenir.'));
        }

        $msg->line(__('Sen değilsen, lütfen aşağıdaki adımları at:'))
            ->line(__('1. Şifreni hemen değiştir.'))
            ->line(__('2. Henüz açmadıysan iki faktörlü doğrulamayı aktive et.'))
            ->line(__('3. Aktif oturumlar sayfasından bilmediğin cihazları sonlandır.'))
            ->action(__('Hesap güvenliğine git'), url('/admin/profile'))
            ->line(__('Bu e-postayı sen tetiklemediysen yine de bir şey yapmana gerek yok — denemenin kaynağını paylaşmıyoruz, kayıt yöneticiye iletildi.'));

        return $msg;
    }
}
