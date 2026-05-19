<?php
namespace App\Modules\Auth\Controllers;

use App\Core\Controller;

class AuthController extends Controller
{
    public function form(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $flash = $this->pullFlash();
        $shared = $this->buildSharedViewData();

        $this->view('auth/login', array_merge($shared, [
            'error' => $flash['error'] ?? null,
            'success' => $flash['success'] ?? null,
        ]));
    }

    public function login(): void
    {
        $input = [];
        if ($this->wantsJson()) {
            $input = json_decode(file_get_contents('php://input'), true) ?: [];
        }
        if (!$input) {
            $input = $_POST;
        }
        $login = $input['login'] ?? '';
        $senha = $input['senha'] ?? '';
        if (!$this->auth->attempt($login, $senha)) {
            $message = 'Usuário ou senha incorretos.';
            if ($this->wantsJson()) {
                $this->json(['message' => $message], 401);
            } else {
                $shared = $this->buildSharedViewData();
                $this->view('auth/login', array_merge($shared, [
                    'error' => $message,
                    'success' => null,
                    'old' => [
                        'login' => $login,
                    ],
                ]), 401);
            }
            return;
        }
        $usuario = $this->auth->user();
        $this->audit->log($usuario['id'], 'login', 'usuarios', $usuario['id'], []);
        if ($this->wantsJson()) {
            $this->json(['message' => 'Autenticado', 'usuario' => $usuario]);
            return;
        }

        $this->setFlash('success', 'Bem-vindo, '.$usuario['nome'].'!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        $usuario = $this->auth->user();
        $this->auth->logout();
        $this->audit->log($usuario['id'] ?? null, 'logout', 'usuarios', $usuario['id'] ?? null, []);
        if ($this->wantsJson()) {
            $this->json(['message' => 'Sessão encerrada']);
            return;
        }

        $this->setFlash('success', 'Sessão encerrada com sucesso.');
        header('Location: /login');
        exit;
    }
}
