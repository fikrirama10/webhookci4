<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use IonAuth\Libraries\IonAuth;

class Auth extends ResourceController
{
    protected $ionAuth;

    public function __construct()
    {
        $this->ionAuth = new IonAuth();
        helper(['form', 'url']);
    }

    // Fungsi untuk login webhook
    public function loginWebhook()
    {
        // Log untuk memastikan fungsi ini dipanggil
        log_message('debug', 'Fungsi loginWebhook dipanggil.');
    
        $data = $this->request->getJSON(true);
    
        if (!isset($data['identity']) || !isset($data['password'])) {
            log_message('error', 'Data JSON tidak lengkap.');
            return $this->respond(['status' => 'error', 'message' => 'Identitas dan password diperlukan.'], 400);
        }
    
        $identity = $data['identity'];
        $password = $data['password'];
        
        if ($this->ionAuth->login($identity, $password)) {
            if ($this->ionAuth->loggedIn()) {
                return $this->respond([
                    'status' => 'success',
                    'message' => 'User is logged in.',
                    'data' => [
                        'user_id' => $this->ionAuth->user()->row()->id,
                        'username' => $this->ionAuth->user()->row()->username,
                        'isLogin'=>$this->ionAuth->loggedIn()
                    ]
                ], 200);
            } else {
                return $this->respond([
                    'status' => 'error',
                    'message' => 'User is not logged in.'
                ], 401);
            }
            // return $this->respond(['status' => 'success', 'message' => 'Login berhasil.'], 200);
        } else {
            log_message('error', 'Login gagal: ' . $this->ionAuth->errors());
            return $this->respond(['status' => 'error', 'message' => $this->ionAuth->errors()], 401);
        }
    }
    

    // Fungsi untuk logout webhook
    public function logoutWebhook()
    {
        $this->ionAuth->logout();
        return $this->respond(['status' => 'success', 'message' => 'Logout berhasil.'], 200);
    }

    public function register()
    {
        // Ambil data dari request
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $email = $this->request->getPost('email');
        
        // Validasi data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[5]|is_unique[users.username]',
            'password' => 'required|min_length[8]',
            'email' => 'required|valid_email|is_unique[users.email]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->respond([
                'status' => 'error',
                'messages' => $validation->getErrors()
            ], 400);
        }

        // Mencoba untuk mendaftar pengguna baru
        // $userModel = new UserModel(); // Ganti dengan model yang sesuai

        $additionalData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
        ];

        // Mendaftar pengguna baru
        $ionAuth =  new IonAuth();
        if ($ionAuth->register($username, $password, $email, $additionalData)) {
            return $this->respond([
                'status' => 'success',
                'message' => 'User registered successfully.',
                'data'=> $ionAuth
            ], 201);
        } else {
            return $this->respond([
                'status' => 'error',
                'message' => $ionAuth->errors()
            ], 400);
        }
    }

}