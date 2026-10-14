<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function respond(int $status, array $data): never
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, ['error' => 'Use POST for this endpoint.']);
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
if ($origin !== '' && $host !== '') {
    $originHost = parse_url($origin, PHP_URL_HOST);
    $requestHost = preg_replace('/:\d+$/', '', $host);
    if (!is_string($originHost) || strcasecmp($originHost, $requestHost) !== 0) {
        respond(403, ['error' => 'This request was not accepted.']);
    }
}

$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > 180000) {
    respond(413, ['error' => 'That request is too large.']);
}
$input = json_decode($raw, true);
if (!is_array($input)) {
    respond(400, ['error' => 'Please send a valid JSON request.']);
}
$action = $input['action'] ?? '';

function config_payload(): array
{
    return [
        'model' => (string)($_SESSION['myai_model'] ?? ''),
        'endpoint' => (string)($_SESSION['myai_endpoint'] ?? 'https://api.openai.com/v1/chat/completions'),
    ];
}

if ($action === 'status') {
    $config = config_payload();
    respond(200, [
        'configured' => $config['model'] !== '' && $config['endpoint'] !== '',
        'keyPresent' => !empty($_SESSION['myai_api_key']),
        'config' => $config,
    ]);
}

if ($action === 'configure') {
    $model = trim((string)($input['model'] ?? ''));
    $endpoint = trim((string)($input['endpoint'] ?? ''));
    $apiKey = trim((string)($input['apiKey'] ?? ''));
    if ($model !== '' && strlen($model) > 150) respond(400, ['error' => 'Model name is too long.']);
    if ($endpoint !== '') {
        $parts = parse_url($endpoint);
        if (!is_array($parts) || !in_array($parts['scheme'] ?? '', ['https', 'http'], true) || empty($parts['host']) || strlen($endpoint) > 1000) {
            respond(400, ['error' => 'Enter a complete HTTP or HTTPS chat completions URL.']);
        }
        if (filter_var($parts['host'], FILTER_VALIDATE_IP) && !in_array($parts['host'], ['127.0.0.1', '::1'], true)) {
            respond(400, ['error' => 'IP address endpoints are not supported.']);
        }
    }
    if ($apiKey !== '' && strlen($apiKey) > 5000) respond(400, ['error' => 'API key is too long.']);
    $_SESSION['myai_model'] = $model;
    $_SESSION['myai_endpoint'] = $endpoint !== '' ? $endpoint : 'https://api.openai.com/v1/chat/completions';
    if ($apiKey !== '') $_SESSION['myai_api_key'] = $apiKey;
    $config = config_payload();
    respond(200, [
        'configured' => $model !== '' && $config['endpoint'] !== '',
        'keyPresent' => !empty($_SESSION['myai_api_key']),
        'config' => $config,
    ]);
}

if ($action === 'forget_key') {
    unset($_SESSION['myai_api_key']);
    $config = config_payload();
    respond(200, [
        'configured' => $config['model'] !== '' && $config['endpoint'] !== '',
        'keyPresent' => false,
        'config' => $config,
    ]);
}

if ($action !== 'chat') respond(400, ['error' => 'Unknown action.']);

$model = (string)($_SESSION['myai_model'] ?? '');
$endpoint = (string)($_SESSION['myai_endpoint'] ?? '');
$apiKey = (string)($_SESSION['myai_api_key'] ?? '');
if ($model === '' || $endpoint === '') respond(409, ['error' => 'Add a model name and API endpoint in settings first.']);
if ($apiKey === '') respond(409, ['error' => 'Add your API key in settings first.']);

$system = trim((string)($input['system'] ?? ''));
$messages = $input['messages'] ?? null;
if (!is_array($messages) || count($messages) < 1 || count($messages) > 30) respond(400, ['error' => 'A conversation must contain between 1 and 30 messages.']);
$payloadMessages = [];
if ($system !== '') $payloadMessages[] = ['role' => 'system', 'content' => mb_substr($system, 0, 5000)];
foreach ($messages as $message) {
    if (!is_array($message) || !in_array($message['role'] ?? '', ['user', 'assistant'], true) || !is_string($message['content'] ?? null)) {
        respond(400, ['error' => 'The conversation contains an invalid message.']);
    }
    $content = trim($message['content']);
    if ($content === '' || strlen($content) > 12000) respond(400, ['error' => 'Each message must contain text and stay under 12,000 characters.']);
    $payloadMessages[] = ['role' => $message['role'], 'content' => $content];
}

if (!function_exists('curl_init')) respond(503, ['error' => 'PHP cURL is not enabled. Enable the curl extension in your XAMPP PHP configuration and restart Apache.']);
$payload = json_encode(['model' => $model, 'messages' => $payloadMessages], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
$curl = curl_init($endpoint);
curl_setopt_array($curl, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $apiKey],
    CURLOPT_POSTFIELDS => $payload,
]);
$body = curl_exec($curl);
$curlError = curl_error($curl);
$httpStatus = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
curl_close($curl);
if ($body === false) respond(502, ['error' => $curlError !== '' ? 'Could not reach the model service. Check the endpoint and network connection.' : 'The model service could not be reached.']);
$result = json_decode((string)$body, true);
if ($httpStatus < 200 || $httpStatus >= 300) {
    $detail = is_array($result) ? (string)($result['error']['message'] ?? '') : '';
    if ($httpStatus === 401 || $httpStatus === 403) respond(502, ['error' => 'The model service rejected the API key. Check that key in your settings.']);
    respond(502, ['error' => $detail !== '' ? 'Model service error: ' . mb_substr($detail, 0, 350) : 'The model service returned an error (HTTP ' . $httpStatus . ').']);
}
$reply = $result['choices'][0]['message']['content'] ?? null;
if (!is_string($reply) || trim($reply) === '') respond(502, ['error' => 'The model returned an empty reply.']);
respond(200, ['reply' => trim($reply)]);
