<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public Attendance $attendance
    ) {}

    public function handle(WhatsAppService $whatsAppService): void
    {
        $whatsAppService->sendAttendanceNotification($this->attendance);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send WhatsApp notification', [
            'attendance_id' => $this->attendance->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
