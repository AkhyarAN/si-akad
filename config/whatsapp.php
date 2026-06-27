<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi WhatsApp Gateway menggunakan Fonnte API
    |
    */

    'provider' => env('WHATSAPP_PROVIDER', 'fonnte'),

    'fonnte' => [
        'api_url' => env('FONNTE_API_URL', 'https://api.fonnte.com/send'),
        'api_token' => env('FONNTE_API_TOKEN', ''),
    ],

    'templates' => [
        'attendance_absent' => "⚠️ *NOTIFIKASI ABSENSI*\n\nYth. Bapak/Ibu :parent_name,\n\nDengan ini kami informasikan bahwa putra/putri Anda:\n\n👤 Nama: *:student_name*\n🏫 Kelas: *:class_name*\n📅 Tanggal: *:date*\n📚 Mata Pelajaran: *:subject*\n⏰ Jam: *:time*\n📋 Status: *:status*\n📝 Catatan: :notes\n\nMohon perhatian dan konfirmasi. Terima kasih.\n\n_SIAKAD SMP_",

        'grade_notification' => "📊 *NOTIFIKASI NILAI*\n\nYth. Bapak/Ibu :parent_name,\n\nNilai terbaru putra/putri Anda:\n\n👤 Nama: *:student_name*\n🏫 Kelas: *:class_name*\n📚 Mata Pelajaran: *:subject*\n📝 Jenis: *:type*\n💯 Nilai: *:score*\n\n_SIAKAD SMP_",

        'report_card' => "📋 *NOTIFIKASI RAPOR*\n\nYth. Bapak/Ibu :parent_name,\n\nRapor semester :semester putra/putri Anda *:student_name* telah tersedia.\n\nSilahkan login ke SIAKAD SMP untuk melihat detail.\n\n_SIAKAD SMP_",
    ],
];
