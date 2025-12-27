<?php
// File: modules/fundraising/helpers.php
require_once __DIR__ . '/../../includes/data.php';

if (!function_exists('fundraising_data_paths')) {
    function fundraising_data_paths(): array
    {
        $baseDir = __DIR__ . '/../../data';
        return [
            'campaigns' => $baseDir . '/fundraising_campaigns.json',
            'donations' => $baseDir . '/fundraising_donations.json',
            'types' => $baseDir . '/fundraising_types.json',
            'lists' => $baseDir . '/fundraising_lists.json',
        ];
    }
}

if (!function_exists('fundraising_default_types')) {
    function fundraising_default_types(): array
    {
        return [
            [
                'id' => 'annual-giving',
                'name' => 'Annual Giving',
                'description' => 'Recurring support for year-round programs and operations.',
            ],
            [
                'id' => 'capital-campaign',
                'name' => 'Capital Campaign',
                'description' => 'Major gifts focused on facilities and long-term investments.',
            ],
            [
                'id' => 'peer-to-peer',
                'name' => 'Peer-to-Peer',
                'description' => 'Supporters raise funds on behalf of the organisation.',
            ],
            [
                'id' => 'matching-gift',
                'name' => 'Matching Gift',
                'description' => 'Corporate or partner matching opportunities for donors.',
            ],
            [
                'id' => 'monthly-membership',
                'name' => 'Monthly Membership',
                'description' => 'Sustaining monthly donors with predictable impact.',
            ],
        ];
    }
}

if (!function_exists('fundraising_default_lists')) {
    function fundraising_default_lists(): array
    {
        return [
            [
                'id' => 'major-donors',
                'name' => 'Major Donors',
                'description' => 'High-capacity donors and leadership circle members.',
                'count' => 24,
            ],
            [
                'id' => 'monthly-donors',
                'name' => 'Monthly Donors',
                'description' => 'Recurring donors committed to monthly giving.',
                'count' => 312,
            ],
            [
                'id' => 'corporate-partners',
                'name' => 'Corporate Partners',
                'description' => 'Businesses and sponsors providing matched funding.',
                'count' => 18,
            ],
            [
                'id' => 'community-ambassadors',
                'name' => 'Community Ambassadors',
                'description' => 'Peer-to-peer fundraising champions and volunteers.',
                'count' => 58,
            ],
        ];
    }
}

if (!function_exists('fundraising_default_campaigns')) {
    function fundraising_default_campaigns(): array
    {
        return [
            [
                'id' => 'campaign-renewal',
                'name' => 'Community Renewal Fund',
                'type_id' => 'annual-giving',
                'list_id' => 'monthly-donors',
                'goal' => 85000,
                'raised' => 52450,
                'status' => 'active',
                'start_date' => date('Y-m-01'),
                'end_date' => date('Y-m-t', strtotime('+2 months')),
                'description' => 'Fuel frontline programs with reliable monthly giving.',
            ],
            [
                'id' => 'campaign-campus',
                'name' => 'Future Campus Initiative',
                'type_id' => 'capital-campaign',
                'list_id' => 'major-donors',
                'goal' => 250000,
                'raised' => 162500,
                'status' => 'active',
                'start_date' => date('Y-m-01', strtotime('-1 month')),
                'end_date' => date('Y-m-t', strtotime('+4 months')),
                'description' => 'Expand facilities to serve more families and students.',
            ],
            [
                'id' => 'campaign-match',
                'name' => 'Match Day Drive',
                'type_id' => 'matching-gift',
                'list_id' => 'corporate-partners',
                'goal' => 60000,
                'raised' => 40200,
                'status' => 'active',
                'start_date' => date('Y-m-d', strtotime('+10 days')),
                'end_date' => date('Y-m-d', strtotime('+40 days')),
                'description' => 'Double impact thanks to partner matching commitments.',
            ],
            [
                'id' => 'campaign-ambassador',
                'name' => 'Run for Change',
                'type_id' => 'peer-to-peer',
                'list_id' => 'community-ambassadors',
                'goal' => 45000,
                'raised' => 31200,
                'status' => 'active',
                'start_date' => date('Y-m-d', strtotime('-15 days')),
                'end_date' => date('Y-m-d', strtotime('+20 days')),
                'description' => 'Ambassadors rally their networks with personal stories.',
            ],
        ];
    }
}

if (!function_exists('fundraising_default_donations')) {
    function fundraising_default_donations(): array
    {
        return [
            [
                'id' => 'donation-1001',
                'donor_name' => 'Lena Morales',
                'donor_email' => 'lena.morales@example.org',
                'amount' => 250,
                'campaign_id' => 'campaign-renewal',
                'list_id' => 'monthly-donors',
                'method' => 'Card',
                'received_at' => date('Y-m-d', strtotime('-2 days')),
            ],
            [
                'id' => 'donation-1002',
                'donor_name' => 'Carter Kim',
                'donor_email' => 'carter.kim@example.org',
                'amount' => 1200,
                'campaign_id' => 'campaign-campus',
                'list_id' => 'major-donors',
                'method' => 'ACH',
                'received_at' => date('Y-m-d', strtotime('-5 days')),
            ],
            [
                'id' => 'donation-1003',
                'donor_name' => 'Nimbus Labs',
                'donor_email' => 'giving@nimbuslabs.co',
                'amount' => 5000,
                'campaign_id' => 'campaign-match',
                'list_id' => 'corporate-partners',
                'method' => 'Wire',
                'received_at' => date('Y-m-d', strtotime('-8 days')),
            ],
            [
                'id' => 'donation-1004',
                'donor_name' => 'Harper Singh',
                'donor_email' => 'harper.singh@example.org',
                'amount' => 75,
                'campaign_id' => 'campaign-renewal',
                'list_id' => 'monthly-donors',
                'method' => 'Card',
                'received_at' => date('Y-m-d', strtotime('-10 days')),
            ],
            [
                'id' => 'donation-1005',
                'donor_name' => 'Eastview Runners',
                'donor_email' => 'team@eastviewrun.org',
                'amount' => 640,
                'campaign_id' => 'campaign-ambassador',
                'list_id' => 'community-ambassadors',
                'method' => 'Check',
                'received_at' => date('Y-m-d', strtotime('-12 days')),
            ],
            [
                'id' => 'donation-1006',
                'donor_name' => 'Olivia Park',
                'donor_email' => 'olivia.park@example.org',
                'amount' => 320,
                'campaign_id' => 'campaign-match',
                'list_id' => 'corporate-partners',
                'method' => 'Card',
                'received_at' => date('Y-m-d', strtotime('-15 days')),
            ],
        ];
    }
}

if (!function_exists('fundraising_ensure_storage')) {
    function fundraising_ensure_storage(): void
    {
        $paths = fundraising_data_paths();
        $defaults = [
            'campaigns' => fundraising_default_campaigns(),
            'donations' => fundraising_default_donations(),
            'types' => fundraising_default_types(),
            'lists' => fundraising_default_lists(),
        ];

        foreach ($paths as $key => $path) {
            if (!is_file($path)) {
                write_json_file($path, $defaults[$key] ?? []);
                continue;
            }

            $data = read_json_file($path);
            if (!is_array($data) || empty($data)) {
                write_json_file($path, $defaults[$key] ?? []);
            }
        }
    }
}

if (!function_exists('fundraising_read_campaigns')) {
    function fundraising_read_campaigns(): array
    {
        fundraising_ensure_storage();
        $paths = fundraising_data_paths();
        $campaigns = read_json_file($paths['campaigns']);
        if (!is_array($campaigns)) {
            return [];
        }
        return array_values(array_filter($campaigns, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('fundraising_read_donations')) {
    function fundraising_read_donations(): array
    {
        fundraising_ensure_storage();
        $paths = fundraising_data_paths();
        $donations = read_json_file($paths['donations']);
        if (!is_array($donations)) {
            return [];
        }
        return array_values(array_filter($donations, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('fundraising_read_types')) {
    function fundraising_read_types(): array
    {
        fundraising_ensure_storage();
        $paths = fundraising_data_paths();
        $types = read_json_file($paths['types']);
        if (!is_array($types)) {
            return [];
        }
        return array_values(array_filter($types, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('fundraising_read_lists')) {
    function fundraising_read_lists(): array
    {
        fundraising_ensure_storage();
        $paths = fundraising_data_paths();
        $lists = read_json_file($paths['lists']);
        if (!is_array($lists)) {
            return [];
        }
        return array_values(array_filter($lists, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('fundraising_format_currency')) {
    function fundraising_format_currency(float $amount, string $currency = 'USD'): string
    {
        $symbol = '$';
        $currency = strtoupper(trim($currency));
        if ($currency !== '' && $currency !== 'USD') {
            $symbol = $currency . ' ';
        }
        return $symbol . number_format($amount, 2);
    }
}

if (!function_exists('fundraising_parse_date')) {
    function fundraising_parse_date(?string $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $timestamp = strtotime($value);
        return $timestamp === false ? 0 : $timestamp;
    }
}
