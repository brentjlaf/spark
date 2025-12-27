<?php
// File: modules/fundraising/view.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/helpers.php';

require_login();

fundraising_ensure_storage();

$campaigns = fundraising_read_campaigns();
$donations = fundraising_read_donations();
$types = fundraising_read_types();
$lists = fundraising_read_lists();

$typesById = [];
foreach ($types as $type) {
    $id = (string) ($type['id'] ?? '');
    if ($id !== '') {
        $typesById[$id] = $type;
    }
}

$listsById = [];
foreach ($lists as $list) {
    $id = (string) ($list['id'] ?? '');
    if ($id !== '') {
        $listsById[$id] = $list;
    }
}

$campaignsById = [];
foreach ($campaigns as $campaign) {
    $id = (string) ($campaign['id'] ?? '');
    if ($id !== '') {
        $campaignsById[$id] = $campaign;
    }
}

$donationTotalsByCampaign = [];
foreach ($donations as $donation) {
    $campaignId = (string) ($donation['campaign_id'] ?? '');
    if ($campaignId === '') {
        continue;
    }
    $donationTotalsByCampaign[$campaignId] = ($donationTotalsByCampaign[$campaignId] ?? 0) + (float) ($donation['amount'] ?? 0);
}

$totalRaised = 0;
foreach ($donations as $donation) {
    $totalRaised += (float) ($donation['amount'] ?? 0);
}

$totalDonations = count($donations);
$donorKeys = [];
foreach ($donations as $donation) {
    $email = strtolower(trim((string) ($donation['donor_email'] ?? '')));
    $name = strtolower(trim((string) ($donation['donor_name'] ?? '')));
    $key = $email !== '' ? $email : $name;
    if ($key !== '') {
        $donorKeys[$key] = true;
    }
}
$totalDonors = count($donorKeys);
$averageGift = $totalDonations > 0 ? $totalRaised / $totalDonations : 0;

$activeCampaigns = 0;
foreach ($campaigns as $campaign) {
    $status = strtolower((string) ($campaign['status'] ?? ''));
    if ($status === 'active') {
        $activeCampaigns++;
    }
}

usort($donations, static function ($a, $b) {
    $aTime = fundraising_parse_date($a['received_at'] ?? '') ?? 0;
    $bTime = fundraising_parse_date($b['received_at'] ?? '') ?? 0;
    return $bTime <=> $aTime;
});

$topCampaigns = $campaigns;
usort($topCampaigns, static function ($a, $b) use ($donationTotalsByCampaign) {
    $aTotal = $donationTotalsByCampaign[$a['id'] ?? ''] ?? (float) ($a['raised'] ?? 0);
    $bTotal = $donationTotalsByCampaign[$b['id'] ?? ''] ?? (float) ($b['raised'] ?? 0);
    if ($aTotal === $bTotal) {
        return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
    }
    return $bTotal <=> $aTotal;
});
$topCampaigns = array_slice($topCampaigns, 0, 3);

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

$monthTotals = [];
$monthLabels = [];
$cursor = new DateTime('first day of this month');
$cursor->setTime(0, 0);
for ($i = 5; $i >= 0; $i--) {
    $month = (clone $cursor)->modify("-{$i} months");
    $key = $month->format('Y-m');
    $monthTotals[$key] = 0.0;
    $monthLabels[$key] = $month->format('M Y');
}
foreach ($donations as $donation) {
    $timestamp = fundraising_parse_date($donation['received_at'] ?? '');
    if ($timestamp === 0) {
        continue;
    }
    $key = date('Y-m', $timestamp);
    if (array_key_exists($key, $monthTotals)) {
        $monthTotals[$key] += (float) ($donation['amount'] ?? 0);
    }
}
?>
<div class="content-section" id="fundraising">
    <div class="fundraising-dashboard a11y-dashboard" data-fundraising-endpoint="modules/fundraising/api.php">
        <header class="a11y-hero">
            <div class="a11y-hero-content">
                <div>
                    <span class="hero-eyebrow">Fundraising Pulse</span>
                    <h2 class="a11y-hero-title">Donations &amp; Campaigns</h2>
                    <p class="a11y-hero-subtitle">Coordinate fundraising campaigns, track donor engagement, and keep leadership up to date with rich reporting.</p>
                </div>
                <div class="a11y-hero-actions">
                    <button type="button" class="a11y-btn a11y-btn--primary">
                        <i class="fa-solid fa-hand-holding-dollar" aria-hidden="true"></i>
                        <span>New Campaign</span>
                    </button>
                    <button type="button" class="a11y-btn a11y-btn--ghost">
                        <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                        <span>Export Reports</span>
                    </button>
                </div>
            </div>
            <div class="a11y-overview-grid">
                <div class="a11y-overview-card">
                    <div class="a11y-overview-value"><?php echo fundraising_format_currency($totalRaised); ?></div>
                    <div class="a11y-overview-label">Total raised</div>
                </div>
                <div class="a11y-overview-card">
                    <div class="a11y-overview-value"><?php echo (int) $activeCampaigns; ?></div>
                    <div class="a11y-overview-label">Active campaigns</div>
                </div>
                <div class="a11y-overview-card">
                    <div class="a11y-overview-value"><?php echo (int) $totalDonors; ?></div>
                    <div class="a11y-overview-label">Donors engaged</div>
                </div>
                <div class="a11y-overview-card">
                    <div class="a11y-overview-value"><?php echo fundraising_format_currency($averageGift); ?></div>
                    <div class="a11y-overview-label">Average gift</div>
                </div>
            </div>
        </header>

        <nav class="fundraising-tabs" aria-label="Fundraising sections" data-fundraising-tabs>
            <button type="button" class="fundraising-tab is-active" id="fundraisingTabOverview" data-fundraising-tab="overview" role="tab" aria-selected="true" aria-controls="fundraisingPanelOverview">Overview</button>
            <button type="button" class="fundraising-tab" id="fundraisingTabCampaigns" data-fundraising-tab="campaigns" role="tab" aria-selected="false" aria-controls="fundraisingPanelCampaigns">Campaigns</button>
            <button type="button" class="fundraising-tab" id="fundraisingTabDonations" data-fundraising-tab="donations" role="tab" aria-selected="false" aria-controls="fundraisingPanelDonations">Donations</button>
            <button type="button" class="fundraising-tab" id="fundraisingTabReports" data-fundraising-tab="reports" role="tab" aria-selected="false" aria-controls="fundraisingPanelReports">Reports</button>
            <button type="button" class="fundraising-tab" id="fundraisingTabLists" data-fundraising-tab="lists" role="tab" aria-selected="false" aria-controls="fundraisingPanelLists">Types &amp; Lists</button>
        </nav>

        <section class="fundraising-section fundraising-tabpanel is-active" id="fundraisingPanelOverview" role="tabpanel" aria-labelledby="fundraisingTabOverview" data-fundraising-panel="overview">
            <div class="a11y-detail-card">
                <header class="a11y-detail-header">
                    <div>
                        <h3 class="text-lg font-semibold">Top campaigns</h3>
                        <p class="text-sm text-slate-600">Monitor progress toward campaign goals and ensure every list is covered.</p>
                    </div>
                </header>
                <div class="a11y-detail-grid">
                    <?php foreach ($topCampaigns as $campaign):
                        $campaignId = (string) ($campaign['id'] ?? '');
                        $raised = $donationTotalsByCampaign[$campaignId] ?? (float) ($campaign['raised'] ?? 0);
                        $goal = (float) ($campaign['goal'] ?? 0);
                        $progress = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
                        $typeName = $typesById[$campaign['type_id'] ?? '']['name'] ?? 'Unassigned';
                        $listName = $listsById[$campaign['list_id'] ?? '']['name'] ?? 'General list';
                    ?>
                    <article class="border border-slate-200 rounded-xl bg-white p-4 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-[0.12em] text-primary-600 font-semibold"><?php echo htmlspecialchars($typeName); ?></p>
                                <h4 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($campaign['name'] ?? 'Untitled Campaign'); ?></h4>
                            </div>
                            <span class="badge"><?php echo htmlspecialchars(ucfirst((string) ($campaign['status'] ?? 'active'))); ?></span>
                        </div>
                        <p class="text-sm text-slate-600"><?php echo htmlspecialchars($campaign['description'] ?? ''); ?></p>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm text-slate-600">
                                <span><?php echo htmlspecialchars($listName); ?></span>
                                <span><?php echo fundraising_format_currency($raised); ?> of <?php echo fundraising_format_currency($goal); ?></span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100">
                                <div class="h-2 rounded-full bg-primary-600" style="width: <?php echo htmlspecialchars((string) $progress); ?>%"></div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="a11y-detail-card">
                <header class="a11y-detail-header">
                    <div>
                        <h3 class="text-lg font-semibold">Recent donations</h3>
                        <p class="text-sm text-slate-600">Keep a pulse on the most recent gifts and their associated lists.</p>
                    </div>
                </header>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Campaign</th>
                                <th>List</th>
                                <th>Amount</th>
                                <th>Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($donations, 0, 5) as $donation):
                                $campaign = $campaignsById[$donation['campaign_id'] ?? ''] ?? [];
                                $listName = $listsById[$donation['list_id'] ?? '']['name'] ?? 'General list';
                            ?>
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($donation['donor_name'] ?? 'Anonymous'); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($donation['donor_email'] ?? ''); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($campaign['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($listName); ?></td>
                                <td><?php echo fundraising_format_currency((float) ($donation['amount'] ?? 0)); ?></td>
                                <td><?php echo htmlspecialchars($donation['received_at'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="fundraising-section fundraising-tabpanel" id="fundraisingPanelCampaigns" role="tabpanel" aria-labelledby="fundraisingTabCampaigns" data-fundraising-panel="campaigns" hidden>
            <div class="a11y-detail-card">
                <header class="a11y-detail-header">
                    <div>
                        <h3 class="text-lg font-semibold">Campaign management</h3>
                        <p class="text-sm text-slate-600">Track goals, lists, and fundraising types from a single command center.</p>
                    </div>
                </header>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th>List</th>
                                <th>Status</th>
                                <th>Goal</th>
                                <th>Raised</th>
                                <th>Progress</th>
                                <th>Timeline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($campaigns as $campaign):
                                $campaignId = (string) ($campaign['id'] ?? '');
                                $raised = $donationTotalsByCampaign[$campaignId] ?? (float) ($campaign['raised'] ?? 0);
                                $goal = (float) ($campaign['goal'] ?? 0);
                                $progress = $goal > 0 ? min(100, ($raised / $goal) * 100) : 0;
                                $typeName = $typesById[$campaign['type_id'] ?? '']['name'] ?? 'Unassigned';
                                $listName = $listsById[$campaign['list_id'] ?? '']['name'] ?? 'General list';
                                $timeline = trim((string) ($campaign['start_date'] ?? ''));
                                $endDate = trim((string) ($campaign['end_date'] ?? ''));
                                if ($endDate !== '') {
                                    $timeline = $timeline !== '' ? $timeline . ' → ' . $endDate : $endDate;
                                }
                            ?>
                            <tr>
                                <td class="font-semibold text-slate-900"><?php echo htmlspecialchars($campaign['name'] ?? 'Untitled Campaign'); ?></td>
                                <td><?php echo htmlspecialchars($typeName); ?></td>
                                <td><?php echo htmlspecialchars($listName); ?></td>
                                <td><span class="badge"><?php echo htmlspecialchars(ucfirst((string) ($campaign['status'] ?? 'active'))); ?></span></td>
                                <td><?php echo fundraising_format_currency($goal); ?></td>
                                <td><?php echo fundraising_format_currency($raised); ?></td>
                                <td><?php echo number_format($progress, 1); ?>%</td>
                                <td><?php echo htmlspecialchars($timeline); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="fundraising-section fundraising-tabpanel" id="fundraisingPanelDonations" role="tabpanel" aria-labelledby="fundraisingTabDonations" data-fundraising-panel="donations" hidden>
            <div class="a11y-detail-card">
                <header class="a11y-detail-header">
                    <div>
                        <h3 class="text-lg font-semibold">Donations &amp; receipts</h3>
                        <p class="text-sm text-slate-600">Review every gift by campaign, type, and donor list.</p>
                    </div>
                </header>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Donor</th>
                                <th>Campaign</th>
                                <th>Type</th>
                                <th>List</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($donations as $donation):
                                $campaign = $campaignsById[$donation['campaign_id'] ?? ''] ?? [];
                                $typeName = $typesById[$campaign['type_id'] ?? '']['name'] ?? 'Unassigned';
                                $listName = $listsById[$donation['list_id'] ?? '']['name'] ?? 'General list';
                            ?>
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($donation['donor_name'] ?? 'Anonymous'); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo htmlspecialchars($donation['donor_email'] ?? ''); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($campaign['name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($typeName); ?></td>
                                <td><?php echo htmlspecialchars($listName); ?></td>
                                <td><?php echo fundraising_format_currency((float) ($donation['amount'] ?? 0)); ?></td>
                                <td><?php echo htmlspecialchars($donation['method'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($donation['received_at'] ?? ''); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="fundraising-section fundraising-tabpanel" id="fundraisingPanelReports" role="tabpanel" aria-labelledby="fundraisingTabReports" data-fundraising-panel="reports" hidden>
            <div class="a11y-detail-grid">
                <section class="a11y-detail-card">
                    <header class="a11y-detail-header">
                        <div>
                            <h3 class="text-lg font-semibold">Fundraising by type</h3>
                            <p class="text-sm text-slate-600">Compare performance across different fundraising motions.</p>
                        </div>
                    </header>
                    <div class="space-y-3">
                        <?php foreach ($types as $type):
                            $typeId = (string) ($type['id'] ?? '');
                            $amount = $typeTotals[$typeId] ?? 0;
                        ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($type['name'] ?? ''); ?></div>
                                <div class="text-xs text-slate-500"><?php echo htmlspecialchars($type['description'] ?? ''); ?></div>
                            </div>
                            <div class="font-semibold text-slate-900"><?php echo fundraising_format_currency((float) $amount); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="a11y-detail-card">
                    <header class="a11y-detail-header">
                        <div>
                            <h3 class="text-lg font-semibold">Fundraising by list</h3>
                            <p class="text-sm text-slate-600">Understand which lists are delivering the most impact.</p>
                        </div>
                    </header>
                    <div class="space-y-3">
                        <?php foreach ($lists as $list):
                            $listId = (string) ($list['id'] ?? '');
                            $amount = $listTotals[$listId] ?? 0;
                        ?>
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-900"><?php echo htmlspecialchars($list['name'] ?? ''); ?></div>
                                <div class="text-xs text-slate-500"><?php echo htmlspecialchars($list['description'] ?? ''); ?></div>
                            </div>
                            <div class="font-semibold text-slate-900"><?php echo fundraising_format_currency((float) $amount); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>

            <div class="a11y-detail-card">
                <header class="a11y-detail-header">
                    <div>
                        <h3 class="text-lg font-semibold">Monthly totals</h3>
                        <p class="text-sm text-slate-600">Track momentum over the last six months.</p>
                    </div>
                </header>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total raised</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthTotals as $monthKey => $amount): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($monthLabels[$monthKey]); ?></td>
                                <td><?php echo fundraising_format_currency((float) $amount); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="fundraising-section fundraising-tabpanel" id="fundraisingPanelLists" role="tabpanel" aria-labelledby="fundraisingTabLists" data-fundraising-panel="lists" hidden>
            <div class="a11y-detail-grid">
                <section class="a11y-detail-card">
                    <header class="a11y-detail-header">
                        <div>
                            <h3 class="text-lg font-semibold">Fundraising types</h3>
                            <p class="text-sm text-slate-600">Define the fundraising motions available to campaign owners.</p>
                        </div>
                    </header>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($types as $type): ?>
                                <tr>
                                    <td class="font-semibold text-slate-900"><?php echo htmlspecialchars($type['name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($type['description'] ?? ''); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="a11y-detail-card">
                    <header class="a11y-detail-header">
                        <div>
                            <h3 class="text-lg font-semibold">Donor lists</h3>
                            <p class="text-sm text-slate-600">Track segments powering targeted outreach.</p>
                        </div>
                    </header>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>List</th>
                                    <th>Members</th>
                                    <th>Summary</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lists as $list): ?>
                                <tr>
                                    <td class="font-semibold text-slate-900"><?php echo htmlspecialchars($list['name'] ?? ''); ?></td>
                                    <td><?php echo number_format((int) ($list['count'] ?? 0)); ?></td>
                                    <td><?php echo htmlspecialchars($list['description'] ?? ''); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </section>
    </div>
</div>
