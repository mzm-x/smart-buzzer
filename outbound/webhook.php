<?php
/**
 * /outbound/webhook.php — Apify Webhook Receiver
 * Receives POST from Apify when a scrape run finishes.
 * Saves run metadata to data/ folder for the dashboard to pick up.
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!$payload || empty($payload['resource'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid payload']));
}

$resource = $payload['resource'];
$runId = $resource['id'] ?? 'unknown';
$status = $resource['status'] ?? 'unknown';
$datasetId = $resource['defaultDatasetId'] ?? '';

// Save webhook data
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

$webhookData = [
    'runId' => $runId,
    'status' => $status,
    'datasetId' => $datasetId,
    'receivedAt' => date('Y-m-d H:i:s'),
    'payload' => $resource
];

file_put_contents(
    $dataDir . '/webhook_' . $runId . '.json',
    json_encode($webhookData, JSON_PRETTY_PRINT)
);

echo json_encode(['success' => true, 'runId' => $runId]);
