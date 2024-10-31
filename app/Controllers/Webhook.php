<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use IonAuth\Libraries\IonAuth;

class Webhook extends ResourceController
{
    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new IonAuth();
    }

    // Fungsi untuk menerima request webhook
    public function handle()
    {
        // Memastikan bahwa hanya user yang sudah login yang bisa mengakses webhook ini
        if (!$this->ionAuth->loggedIn()) {
            return $this->failUnauthorized('User tidak terotorisasi.');
        }

        // Ambil data dari request (misalnya, JSON payload dari webhook)
        $data = $this->request->getJSON(true);

        // Validasi data yang diterima
        if (!isset($data['event_type']) || !isset($data['payload'])) {
            return $this->fail('Data tidak lengkap.', 400);
        }

        // Proses data berdasarkan jenis event
        switch ($data['event_type']) {
            case 'user_registered':
                // Proses event user baru terdaftar
                $this->processUserRegistered($data['payload']);
                break;

            case 'order_placed':
                // Proses event order baru dibuat
                $this->processOrderPlaced($data['payload']);
                break;

            // Tambahkan case lain jika ada tipe event tambahan
            default:
                return $this->fail('Event tidak dikenal.', 400);
        }

        // Berikan respon sukses
        return $this->respond(['status' => 'success'], 200);
    }

    private function processUserRegistered($payload)
    {
        // Contoh pemrosesan event user_registered
        log_message('info', 'User baru terdaftar dengan data: ' . json_encode($payload));
        // Lakukan aksi lain yang dibutuhkan
    }

    private function processOrderPlaced($payload)
    {
        // Contoh pemrosesan event order_placed
        log_message('info', 'Order baru dibuat dengan data: ' . json_encode($payload));
        // Lakukan aksi lain yang dibutuhkan
    }
}
