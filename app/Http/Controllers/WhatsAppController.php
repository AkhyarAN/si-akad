<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ParentModel;
use App\Models\Teacher;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    public function index()
    {
        $status = Setting::get('wa_status', 'inactive');
        $token = Setting::get('wa_api_token', '');
        
        return view('master.whatsapp', compact('status', 'token'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'wa_status' => 'required|in:active,inactive',
            'wa_api_token' => 'nullable|string',
        ]);

        Setting::set('wa_status', $request->wa_status);
        Setting::set('wa_api_token', $request->wa_api_token ?? '');

        return back()->with('success', 'Pengaturan WhatsApp Gateway berhasil diperbarui.');
    }

    public function sendTest(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $status = Setting::get('wa_status', 'inactive');
        if ($status !== 'active') {
            return back()->with('error', 'Gagal mengirim pesan. Gateway sedang dinonaktifkan.');
        }

        $result = $waService->sendMessage($request->phone, $request->message);

        if ($result['success']) {
            return back()->with('success', 'Pesan uji coba berhasil dikirim!');
        } else {
            return back()->with('error', 'Gagal mengirim pesan: ' . (is_string($result['response']) ? $result['response'] : json_encode($result['response'])));
        }
    }

    public function sendBroadcast(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'target' => 'required|in:all_parents,all_teachers',
            'message' => 'required|string',
        ]);

        $status = Setting::get('wa_status', 'inactive');
        if ($status !== 'active') {
            return back()->with('error', 'Gagal mengirim broadcast. Gateway sedang dinonaktifkan.');
        }

        $phones = [];
        if ($request->target === 'all_parents') {
            $phones = ParentModel::whereNotNull('whatsapp_number')->pluck('whatsapp_number')->toArray();
        } elseif ($request->target === 'all_teachers') {
            $phones = Teacher::whereNotNull('phone')->pluck('phone')->toArray();
        }

        if (empty($phones)) {
            return back()->with('error', 'Tidak ada target nomor WhatsApp yang ditemukan.');
        }

        // Send broadcast
        $waService->sendBulkMessage($phones, $request->message);

        return back()->with('success', 'Pesan broadcast telah dimasukkan ke antrean pengiriman untuk ' . count($phones) . ' kontak.');
    }
}
