<?php

class AuthController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function login(): void
    {
        if (currentUser()) {
            redirect('dashboard/index');
        }

        if (isPost()) {
            csrf_check();
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            set_old($_POST);

            if ($email === '' || $password === '') {
                flash('error', 'Preencha e-mail e senha.');
                redirect('auth/login');
            }

            $user = $this->users->findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                flash('error', 'Credenciais inválidas.');
                redirect('auth/login');
            }

            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'restaurant_id' => (int) $user['restaurant_id'],
            ];
            $_SESSION['restaurant'] = [
                'id' => (int) $user['restaurant_id'],
                'name' => $user['restaurant_name'],
                'slug' => $user['restaurant_slug'],
                'logo_path' => $user['restaurant_logo_path'] ?? '',
                'primary_color' => $user['restaurant_primary_color'] ?? '#FF7A00',
                'primary_color_2' => $user['restaurant_primary_color_2'] ?? '#FF9F45',
                'sidebar_color_a' => $user['restaurant_sidebar_color_a'] ?? '#050505',
                'sidebar_color_b' => $user['restaurant_sidebar_color_b'] ?? '#151515',
            ];
            clear_old();
            flash('success', 'Login realizado com sucesso.');
            redirect('dashboard/index');
        }

        clear_old();
        view('auth/login', [], false);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        session_start();
        flash('success', 'Você saiu da sua conta.');
        redirect('auth/login');
    }
}
