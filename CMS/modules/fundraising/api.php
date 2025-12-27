<?php
// File: modules/fundraising/api.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/helpers.php';

require_login();

fundraising_ensure_storage();

$campaigns = fundraising_read_campaigns();
$donations = fundraising_read_donations();
$types = fundraising_read_types();
$lists = fundraising_read_lists();

$action = $_GET['action'] ?? $_POST['action'] ?? 'overview';
$action = strtolower(trim((string) $action));

switch ($action) {
    case 'overview':
        handle_overview($campaigns, $donations);
        break;
    case 'list_campaigns':
        respond_json(['campaigns' => $campaigns]);
        break;
    case 'list_donations':
        respond_json(['donations' => $donations]);
        break;
    case 'report_summary':
        handle_report_summary($campaigns, $donations, $types, $lists);
        break;
    default:
        respond_json(['error' => 'Unknown action.'], 400);
}

function respond_json(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

function handle_overview(array $campaigns, array $donations): void
{
    $totalRaised = 0;
    $donorKeys = [];
    foreach ($donations as $donation) {
        $totalRaised += (float) ($donation['amount'] ?? 0);
        $email = strtolower(trim((string) ($donation['donor_email'] ?? '')));
        $name = strtolower(trim((string) ($donation['donor_name'] ?? '')));
        $key = $email !== '' ? $email : $name;
        if ($key !== '') {
            $donorKeys[$key] = true;
        }
    }

    $activeCampaigns = 0;
    foreach ($campaigns as $campaign) {
        $status = strtolower((string) ($campaign['status'] ?? ''));
        if ($status === 'active') {
            $activeCampaigns++;
        }
    }

    respond_json([
        'stats' => [
            'total_raised' => $totalRaised,
            'total_donations' => count($donations),
            'donors' => count($donorKeys),
            'active_campaigns' => $activeCampaigns,
        ],
    ]);
}

function handle_report_summary(array $campaigns, array $donations, array $types, array $lists): void
{
    $campaignsById = [];
    foreach ($campaigns as $campaign) {
        $campaignsById[(string) ($campaign['id'] ?? '')] = $campaign;
    }

    $typeTotals = [];
    $listTotals = [];
    foreach ($donations as $donation) {
        $amount = (float) ($donation['amount'] ?? 0);
        $listId = (string) ($donation['list_id'] ?? '');
        if ($listId !== '') {
            $listTotals[$listId] = ($listTotals[$listId] ?? 0) + $amount;
        }

        $campaignId = (string) ($donation['campaign_id'] ?? '');
        $campaign = $campaignsById[$campaignId] ?? null;
        $typeId = $campaign['type_id'] ?? '';
        if ($typeId !== '') {
            $typeTotals[$typeId] = ($typeTotals[$typeId] ?? 0) + $amount;
        }
    }

    $typesSummary = [];
    foreach ($types as $type) {
        $id = (string) ($type['id'] ?? '');
        $typesSummary[] = [
            'id' => $id,
            'name' => $type['name'] ?? '',
            'total' => $typeTotals[$id] ?? 0,
        ];
    }

    $listsSummary = [];
    foreach ($lists as $list) {
        $id = (string) ($list['id'] ?? '');
        $listsSummary[] = [
            'id' => $id,
            'name' => $list['name'] ?? '',
            'total' => $listTotals[$id] ?? 0,
        ];
    }

    respond_json([
        'types' => $typesSummary,
        'lists' => $listsSummary,
    ]);
}
