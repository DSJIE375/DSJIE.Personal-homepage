<?php

function getDataPath(): string
{
    return __DIR__ . '/../data/content.json';
}

function loadData(): array
{
    $path = getDataPath();
    if (!file_exists($path)) {
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, json_encode([
            'site_links' => [],
            'gallery' => [],
            'contact' => [
                'details' => []
            ],
            'about' => [
                'intro' => '',
                'info' => [],
                'skills' => []
            ],
            'resume' => [
                'education' => [],
                'experience' => []
            ]
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $json = file_get_contents($path);
    $data = json_decode($json, true);

    if (!is_array($data)) {
        $data = [
            'site_links' => [],
            'gallery' => [],
            'about' => ['info' => [], 'skills' => []],
            'resume' => ['education' => [], 'experience' => []],
        ];
    }

    if (!isset($data['site_links'])) {
        $data['site_links'] = [];
    }
    if (!isset($data['gallery'])) {
        $data['gallery'] = [];
    }
    if (!isset($data['contact'])) {
        $data['contact'] = ['details' => []];
    }
    if (!isset($data['about'])) {
        $data['about'] = ['intro' => '', 'info' => [], 'skills' => []];
    }
    if (!isset($data['about']['intro'])) {
        $data['about']['intro'] = '';
    }
    if (!isset($data['resume'])) {
        $data['resume'] = ['education' => [], 'experience' => []];
    }

    // 确保所有条目都有id
    $data['site_links'] = ensureIds($data['site_links']);
    $data['gallery'] = ensureIds($data['gallery']);
    $data['contact']['details'] = ensureIds($data['contact']['details']);
    $data['about']['info'] = ensureIds($data['about']['info']);
    $data['about']['skills'] = ensureIds($data['about']['skills']);
    $data['resume']['education'] = ensureIds($data['resume']['education']);
    $data['resume']['experience'] = ensureIds($data['resume']['experience']);
    if (isset($data['home']['social_links'])) {
        $data['home']['social_links'] = ensureIds($data['home']['social_links']);
    }

    return $data;
}

function saveData(array $data): bool
{
    $path = getDataPath();
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function nextId(array $items): int
{
    $max = 0;
    foreach ($items as $item) {
        if (isset($item['id']) && is_numeric($item['id'])) {
            $max = max($max, (int)$item['id']);
        }
    }
    return $max + 1;
}

function ensureIds(array $items): array
{
    $nextId = 1;
    foreach ($items as &$item) {
        if (!isset($item['id']) || !is_numeric($item['id'])) {
            $item['id'] = $nextId++;
        } else {
            $nextId = max($nextId, (int)$item['id'] + 1);
        }
    }
    unset($item);
    return $items;
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    session_start();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: admin.php');
        exit;
    }
}

function login(string $password): bool
{
    // 这里可以根据需要修改密码
    $adminPassword = '12345678'; // 请务必修改默认密码！ --- IGNORE ---
    if ($password === $adminPassword) {
        session_start();
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function logout(): void
{
    session_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function getMessagesPath(): string
{
    return __DIR__ . '/../data/messages.json';
}

function loadMessages(): array
{
    $path = getMessagesPath();
    if (!file_exists($path)) {
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    $json = file_get_contents($path);
    $messages = json_decode($json, true);

    if (!is_array($messages)) {
        $messages = [];
    }

    // 确保所有留言都有id
    $messages = ensureIds($messages);

    return $messages;
}

function saveMessages(array $messages): bool
{
    $path = getMessagesPath();
    return file_put_contents($path, json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}
