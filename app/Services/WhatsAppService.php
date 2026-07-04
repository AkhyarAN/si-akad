<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceNotification;
use App\Models\Grade;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.fonnte.api_url');
        $this->apiToken = \App\Models\Setting::get('wa_api_token', '');
    }

    /**
     * Kirim pesan WhatsApp
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (\App\Models\Setting::get('wa_status', 'inactive') !== 'active') {
            return [
                'success' => false,
                'response' => 'WhatsApp Gateway is inactive in settings.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
            ])->post($this->apiUrl, [
                'target' => $phone,
                'message' => $message,
            ]);

            $result = $response->json();

            Log::info('WhatsApp message sent', [
                'phone' => $phone,
                'status' => $response->status(),
                'response' => $result,
            ]);

            return [
                'success' => $response->successful(),
                'response' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp message failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi absensi ke orang tua
     */
    public function sendAttendanceNotification(Attendance $attendance): bool
    {
        $student = $attendance->student;
        $parent = $student->parent;

        if (!$parent || !$parent->whatsapp_number) {
            Log::warning('No parent WhatsApp number for student: ' . $student->name);
            return false;
        }

        $template = config('whatsapp.templates.attendance_absent');
        $message = str_replace(
            [':parent_name', ':student_name', ':class_name', ':date', ':subject', ':time', ':status', ':notes'],
            [
                $parent->name,
                $student->name,
                $student->classRoom?->name ?? '-',
                $attendance->date->format('d/m/Y'),
                $attendance->subject?->name ?? '-',
                $attendance->schedule?->time_range ?? '-',
                strtoupper($attendance->status),
                $attendance->notes ?? 'Tidak ada catatan',
            ],
            $template
        );

        $result = $this->sendMessage($parent->whatsapp_number, $message);

        // Log notification
        AttendanceNotification::create([
            'attendance_id' => $attendance->id,
            'parent_id' => $parent->id,
            'message' => $message,
            'whatsapp_number' => $parent->whatsapp_number,
            'status' => $result['success'] ? 'sent' : 'failed',
            'response' => json_encode($result['response']),
            'sent_at' => now(),
        ]);

        return $result['success'];
    }

    /**
     * Kirim notifikasi nilai ke orang tua
     */
    public function sendGradeNotification(Grade $grade): bool
    {
        $student = $grade->student;
        $parent = $student->parent;

        if (!$parent || !$parent->whatsapp_number) {
            return false;
        }

        $template = config('whatsapp.templates.grade_notification');
        $message = str_replace(
            [':parent_name', ':student_name', ':class_name', ':subject', ':type', ':score'],
            [
                $parent->name,
                $student->name,
                $student->classRoom?->name ?? '-',
                $grade->subject?->name ?? '-',
                $grade->type_label,
                $grade->score,
            ],
            $template
        );

        $result = $this->sendMessage($parent->whatsapp_number, $message);
        return $result['success'];
    }

    /**
     * Kirim pesan massal
     */
    public function sendBulkMessage(array $phones, string $message): array
    {
        $results = [];
        foreach ($phones as $phone) {
            $results[$phone] = $this->sendMessage($phone, $message);
        }
        return $results;
    }
}
