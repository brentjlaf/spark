<?php
// File: modules/events/helpers.php
require_once __DIR__ . '/../../includes/data.php';

if (!function_exists('events_data_paths')) {
    function events_data_paths(): array
    {
        $baseDir = __DIR__ . '/../../data';
        return [
            'events' => $baseDir . '/events.json',
            'orders' => $baseDir . '/event_orders.json',
            'categories' => $baseDir . '/event_categories.json',
        ];
    }
}

if (!function_exists('events_ensure_storage')) {
    function events_ensure_storage(): void
    {
        $paths = events_data_paths();
        foreach ($paths as $path) {
            if (!is_file($path)) {
                file_put_contents($path, "[]\n");
            }
        }
    }
}

if (!function_exists('events_slugify')) {
    function events_slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');
        if ($value === '') {
            return uniqid('category_', false);
        }
        return $value;
    }
}

if (!function_exists('events_unique_slug')) {
    function events_unique_slug(string $desired, array $categories, ?string $currentId = null): string
    {
        $slug = events_slugify($desired);
        $base = $slug;
        if ($base === '') {
            $base = uniqid('category_', false);
        }
        $slug = $base;
        $existing = [];
        foreach ($categories as $category) {
            if (!is_array($category)) {
                continue;
            }
            $id = (string) ($category['id'] ?? '');
            if ($currentId !== null && $id === $currentId) {
                continue;
            }
            $key = strtolower((string) ($category['slug'] ?? ''));
            if ($key !== '') {
                $existing[$key] = true;
            }
        }
        $candidate = strtolower($slug);
        $suffix = 2;
        while ($candidate === '' || isset($existing[$candidate])) {
            $slug = $base . '-' . $suffix;
            $candidate = strtolower($slug);
            $suffix++;
        }
        return $slug;
    }
}

if (!function_exists('events_read_events')) {
    function events_read_events(): array
    {
        events_ensure_storage();
        $paths = events_data_paths();
        $events = read_json_file($paths['events']);
        if (!is_array($events)) {
            return [];
        }
        return array_values(array_filter($events, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('events_read_orders')) {
    function events_read_orders(): array
    {
        events_ensure_storage();
        $paths = events_data_paths();
        $orders = read_json_file($paths['orders']);
        if (!is_array($orders)) {
            return [];
        }
        return array_values(array_filter($orders, static function ($item) {
            return is_array($item) && isset($item['id']);
        }));
    }
}

if (!function_exists('events_read_categories')) {
    function events_read_categories(): array
    {
        events_ensure_storage();
        $paths = events_data_paths();
        $categories = read_json_file($paths['categories']);
        if (!is_array($categories)) {
            return [];
        }
        return events_sort_categories($categories);
    }
}

if (!function_exists('events_write_events')) {
    function events_write_events(array $events): bool
    {
        $paths = events_data_paths();
        return write_json_file($paths['events'], array_values($events));
    }
}

if (!function_exists('events_write_orders')) {
    function events_write_orders(array $orders): bool
    {
        $paths = events_data_paths();
        return write_json_file($paths['orders'], array_values($orders));
    }
}

if (!function_exists('events_sort_categories')) {
    function events_sort_categories(array $categories): array
    {
        $normalized = [];
        foreach ($categories as $category) {
            if (!is_array($category)) {
                continue;
            }
            $id = (string) ($category['id'] ?? '');
            $name = trim((string) ($category['name'] ?? ''));
            $slug = (string) ($category['slug'] ?? '');
            if ($id === '' || $name === '') {
                continue;
            }
            $normalized[] = [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'created_at' => $category['created_at'] ?? null,
                'updated_at' => $category['updated_at'] ?? null,
            ];
        }

        usort($normalized, static function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        return array_values($normalized);
    }
}

if (!function_exists('events_write_categories')) {
    function events_write_categories(array $categories): bool
    {
        $paths = events_data_paths();
        return write_json_file($paths['categories'], events_sort_categories($categories));
    }
}

if (!function_exists('events_find_event')) {
    function events_find_event(array $events, $id): ?array
    {
        foreach ($events as $event) {
            if ((string) ($event['id'] ?? '') === (string) $id) {
                return $event;
            }
        }
        return null;
    }
}

if (!function_exists('events_find_order')) {
    function events_find_order(array $orders, $id): ?array
    {
        foreach ($orders as $order) {
            if ((string) ($order['id'] ?? '') === (string) $id) {
                return $order;
            }
        }
        return null;
    }
}

if (!function_exists('events_normalize_ticket')) {
    function events_normalize_ticket(array $ticket): array
    {
        $ticket['id'] = $ticket['id'] ?? uniqid('tkt_', true);
        $ticket['name'] = trim((string) ($ticket['name'] ?? '')); 
        $ticket['price'] = (float) ($ticket['price'] ?? 0);
        $ticket['quantity'] = max(0, (int) ($ticket['quantity'] ?? 0));
        $ticket['enabled'] = filter_var($ticket['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN);
        return $ticket;
    }
}

if (!function_exists('events_filter_category_ids')) {
    function events_filter_category_ids($categoryIds, array $categories): array
    {
        if (!is_array($categoryIds)) {
            return [];
        }
        $validIds = [];
        $known = [];
        foreach ($categories as $category) {
            if (!is_array($category)) {
                continue;
            }
            $id = (string) ($category['id'] ?? '');
            if ($id !== '') {
                $known[$id] = true;
            }
        }
        foreach ($categoryIds as $categoryId) {
            $categoryId = (string) $categoryId;
            if ($categoryId === '' || !isset($known[$categoryId])) {
                continue;
            }
            if (!in_array($categoryId, $validIds, true)) {
                $validIds[] = $categoryId;
            }
        }
        return $validIds;
    }
}

if (!function_exists('events_default_recurrence')) {
    function events_default_recurrence(): array
    {
        return [
            'frequency' => 'none',
            'interval' => 1,
            'unit' => 'days',
            'end_type' => 'never',
            'end_count' => 0,
            'end_date' => '',
        ];
    }
}

if (!function_exists('events_normalize_recurrence')) {
    function events_normalize_recurrence($recurrence): array
    {
        $defaults = events_default_recurrence();
        if (!is_array($recurrence)) {
            $recurrence = [];
        }

        $frequency = strtolower((string) ($recurrence['frequency'] ?? 'none'));
        if (!in_array($frequency, ['none', 'daily', 'weekly', 'custom'], true)) {
            $frequency = 'none';
        }

        $interval = (int) ($recurrence['interval'] ?? 1);
        if ($interval < 1) {
            $interval = 1;
        }

        $unit = strtolower((string) ($recurrence['unit'] ?? 'days'));
        if (!in_array($unit, ['days', 'weeks'], true)) {
            $unit = 'days';
        }

        if ($frequency === 'daily') {
            $unit = 'days';
        } elseif ($frequency === 'weekly') {
            $unit = 'weeks';
        }

        $endType = strtolower((string) ($recurrence['end_type'] ?? 'never'));
        if (!in_array($endType, ['never', 'after', 'on_date'], true)) {
            $endType = 'never';
        }

        $endCount = (int) ($recurrence['end_count'] ?? 0);
        if ($endCount < 1) {
            $endCount = 0;
        }

        $endDate = trim((string) ($recurrence['end_date'] ?? ''));
        if ($endDate !== '') {
            $timestamp = strtotime($endDate);
            if ($timestamp === false) {
                $endDate = '';
            } else {
                $endDate = gmdate('Y-m-d', $timestamp);
            }
        }

        if ($frequency === 'none') {
            $endType = 'never';
            $endCount = 0;
            $endDate = '';
        }

        if ($endType !== 'after') {
            $endCount = 0;
        }

        if ($endType !== 'on_date') {
            $endDate = '';
        }

        return [
            'frequency' => $frequency,
            'interval' => $interval,
            'unit' => $unit,
            'end_type' => $endType,
            'end_count' => $endCount,
            'end_date' => $endDate,
        ] + $defaults;
    }
}

if (!function_exists('events_recurrence_summary')) {
    function events_recurrence_summary(array $event): string
    {
        $recurrence = events_normalize_recurrence($event['recurrence'] ?? []);
        if ($recurrence['frequency'] === 'none') {
            return '';
        }

        $parts = [];
        $interval = max(1, (int) ($recurrence['interval'] ?? 1));

        switch ($recurrence['frequency']) {
            case 'daily':
                $parts[] = $interval === 1 ? 'Repeats daily' : sprintf('Repeats every %d days', $interval);
                break;
            case 'weekly':
                $parts[] = $interval === 1 ? 'Repeats weekly' : sprintf('Repeats every %d weeks', $interval);
                break;
            case 'custom':
                $unit = $recurrence['unit'] === 'weeks' ? 'week' : 'day';
                $parts[] = sprintf('Repeats every %d %s', $interval, $interval === 1 ? $unit : $unit . 's');
                break;
            default:
                $parts[] = 'Repeats';
                break;
        }

        if ($recurrence['end_type'] === 'after' && $recurrence['end_count'] > 0) {
            $count = (int) $recurrence['end_count'];
            $parts[] = $count === 1 ? 'Ends after 1 occurrence' : sprintf('Ends after %d occurrences', $count);
        } elseif ($recurrence['end_type'] === 'on_date' && $recurrence['end_date'] !== '') {
            $timestamp = strtotime($recurrence['end_date']);
            if ($timestamp !== false) {
                $parts[] = 'Ends on ' . date('M j, Y', $timestamp);
            }
        }

        return implode(' · ', array_filter($parts));
    }
}

if (!function_exists('events_normalize_event')) {
    function events_normalize_event(array $event, array $categories = []): array
    {
        $now = gmdate('c');
        if (empty($event['id'])) {
            $event['id'] = uniqid('evt_', true);
            $event['created_at'] = $now;
        }
        $event['title'] = trim((string) ($event['title'] ?? 'Untitled Event'));
        $event['description'] = (string) ($event['description'] ?? '');
        $event['location'] = trim((string) ($event['location'] ?? ''));
        $event['image'] = trim((string) ($event['image'] ?? ''));
        $event['start'] = (string) ($event['start'] ?? '');
        $event['end'] = (string) ($event['end'] ?? '');
        $event['status'] = in_array($event['status'] ?? '', ['draft', 'published', 'ended'], true)
            ? $event['status']
            : 'draft';
        $event['tickets'] = array_values(array_map('events_normalize_ticket', $event['tickets'] ?? []));
        $event['categories'] = events_filter_category_ids($event['categories'] ?? [], $categories);
        $event['recurrence'] = events_normalize_recurrence($event['recurrence'] ?? []);
        $event['recurrence_summary'] = events_recurrence_summary($event);
        if (!isset($event['published_at']) && $event['status'] === 'published') {
            $event['published_at'] = $now;
        }
        $event['updated_at'] = $now;
        return $event;
    }
}

if (!function_exists('events_ticket_capacity')) {
    function events_ticket_capacity(array $event, bool $onlyEnabled = false): int
    {
        $tickets = $event['tickets'] ?? [];
        $capacity = 0;
        foreach ($tickets as $ticket) {
            if ($onlyEnabled && empty($ticket['enabled'])) {
                continue;
            }
            $capacity += max(0, (int) ($ticket['quantity'] ?? 0));
        }
        return $capacity;
    }
}

if (!function_exists('events_ticket_price_lookup')) {
    function events_ticket_price_lookup(array $event): array
    {
        $lookup = [];
        foreach ($event['tickets'] ?? [] as $ticket) {
            $ticketId = (string) ($ticket['id'] ?? '');
            if ($ticketId === '') {
                continue;
            }
            $lookup[$ticketId] = [
                'name' => (string) ($ticket['name'] ?? ''),
                'price' => (float) ($ticket['price'] ?? 0),
            ];
        }
        return $lookup;
    }
}

if (!function_exists('events_event_ticket_options')) {
    function events_event_ticket_options(?array $event): array
    {
        if (!is_array($event)) {
            return [];
        }
        $options = [];
        foreach ($event['tickets'] ?? [] as $ticket) {
            if (!is_array($ticket)) {
                continue;
            }
            $ticketId = (string) ($ticket['id'] ?? '');
            if ($ticketId === '') {
                continue;
            }
            $options[] = [
                'ticket_id' => $ticketId,
                'name' => (string) ($ticket['name'] ?? 'Ticket'),
                'price' => (float) ($ticket['price'] ?? 0),
            ];
        }
        usort($options, static function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });
        return array_values($options);
    }
}

if (!function_exists('events_normalize_order_tickets')) {
    function events_normalize_order_tickets(array $tickets, array $events, string $eventId): array
    {
        $lookup = [];
        if ($eventId !== '') {
            $event = events_find_event($events, $eventId);
            if ($event) {
                $lookup = events_ticket_price_lookup($event);
            }
        }
        $normalized = [];
        foreach ($tickets as $ticket) {
            if (!is_array($ticket)) {
                continue;
            }
            $ticketId = (string) ($ticket['ticket_id'] ?? '');
            if ($ticketId === '') {
                continue;
            }
            $quantity = max(0, (int) ($ticket['quantity'] ?? 0));
            $price = isset($ticket['price']) ? (float) $ticket['price'] : ($lookup[$ticketId]['price'] ?? 0);
            if ($quantity === 0) {
                continue;
            }
            if (!isset($normalized[$ticketId])) {
                $normalized[$ticketId] = [
                    'ticket_id' => $ticketId,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            } else {
                $normalized[$ticketId]['quantity'] += $quantity;
                $normalized[$ticketId]['price'] = $price;
            }
        }
        return array_values($normalized);
    }
}

if (!function_exists('events_normalize_order')) {
    function events_normalize_order(array $order, array $events, ?array $original = null): array
    {
        $id = isset($order['id']) ? trim((string) $order['id']) : '';
        if ($id === '' && $original) {
            $id = (string) ($original['id'] ?? '');
        }
        $order['id'] = $id;
        $eventId = isset($order['event_id']) ? trim((string) $order['event_id']) : '';
        if ($eventId === '' && $original) {
            $eventId = (string) ($original['event_id'] ?? '');
        }
        $order['event_id'] = $eventId;
        $order['buyer_name'] = trim((string) ($order['buyer_name'] ?? ($original['buyer_name'] ?? '')));
        $status = strtolower((string) ($order['status'] ?? ($original['status'] ?? 'paid')));
        $allowed = ['paid', 'pending', 'refunded'];
        if (!in_array($status, $allowed, true)) {
            $status = 'paid';
        }
        $order['status'] = $status;
        $orderedAt = $order['ordered_at'] ?? ($original['ordered_at'] ?? '');
        if ($orderedAt !== '') {
            $timestamp = strtotime((string) $orderedAt);
            if ($timestamp !== false) {
                $order['ordered_at'] = gmdate('c', $timestamp);
            } elseif ($original && isset($original['ordered_at'])) {
                $order['ordered_at'] = $original['ordered_at'];
            }
        } elseif ($original && isset($original['ordered_at'])) {
            $order['ordered_at'] = $original['ordered_at'];
        } else {
            $order['ordered_at'] = '';
        }

        $order['tickets'] = events_normalize_order_tickets($order['tickets'] ?? [], $events, $eventId);

        $amount = 0.0;
        foreach ($order['tickets'] as $ticket) {
            $amount += (float) $ticket['price'] * (int) $ticket['quantity'];
        }
        $order['amount'] = round($amount, 2);

        $now = gmdate('c');
        if ($original && isset($original['created_at'])) {
            $order['created_at'] = $original['created_at'];
        } elseif (empty($order['created_at'])) {
            $order['created_at'] = $now;
        }
        $order['updated_at'] = $now;

        return $order;
    }
}

if (!function_exists('events_order_line_items')) {
    function events_order_line_items(array $order, array $events): array
    {
        $event = events_find_event($events, $order['event_id'] ?? '');
        $lookup = $event ? events_ticket_price_lookup($event) : [];
        $lines = [];
        foreach ($order['tickets'] ?? [] as $ticket) {
            if (!is_array($ticket)) {
                continue;
            }
            $ticketId = (string) ($ticket['ticket_id'] ?? '');
            if ($ticketId === '') {
                continue;
            }
            $quantity = max(0, (int) ($ticket['quantity'] ?? 0));
            $price = (float) ($ticket['price'] ?? 0);
            if ($quantity === 0) {
                continue;
            }
            $name = $lookup[$ticketId]['name'] ?? ($ticket['name'] ?? 'Ticket');
            if ($price === 0 && isset($lookup[$ticketId]['price'])) {
                $price = (float) $lookup[$ticketId]['price'];
            }
            $lines[] = [
                'ticket_id' => $ticketId,
                'name' => $name,
                'price' => round($price, 2),
                'quantity' => $quantity,
                'subtotal' => round($price * $quantity, 2),
            ];
        }
        return $lines;
    }
}

if (!function_exists('events_order_summary')) {
    function events_order_summary(array $order, array $events): array
    {
        $lines = events_order_line_items($order, $events);
        $tickets = 0;
        $amount = 0.0;
        foreach ($lines as $line) {
            $tickets += (int) $line['quantity'];
            $amount += (float) $line['subtotal'];
        }
        $status = strtolower((string) ($order['status'] ?? 'paid'));
        $event = events_find_event($events, $order['event_id'] ?? '');
        return [
            'id' => (string) ($order['id'] ?? ''),
            'event_id' => (string) ($order['event_id'] ?? ''),
            'event' => $event['title'] ?? 'Event',
            'buyer_name' => (string) ($order['buyer_name'] ?? ''),
            'tickets' => $tickets,
            'amount' => round($amount, 2),
            'status' => $status,
            'ordered_at' => (string) ($order['ordered_at'] ?? ''),
            'line_items' => $lines,
        ];
    }
}

if (!function_exists('events_order_detail')) {
    function events_order_detail(array $order, array $events): array
    {
        $summary = events_order_summary($order, $events);
        $event = events_find_event($events, $summary['event_id']);
        $subtotal = (float) $summary['amount'];
        $isRefunded = $summary['status'] === 'refunded';
        $refunds = $isRefunded ? $subtotal : 0.0;
        return [
            'id' => $summary['id'],
            'event_id' => $summary['event_id'],
            'event' => [
                'id' => $event['id'] ?? '',
                'title' => $event['title'] ?? ($summary['event'] ?? 'Event'),
            ],
            'buyer_name' => $summary['buyer_name'],
            'status' => $summary['status'],
            'ordered_at' => $summary['ordered_at'],
            'line_items' => $summary['line_items'],
            'totals' => [
                'subtotal' => round($subtotal, 2),
                'refunds' => round($refunds, 2),
                'net' => round($subtotal - $refunds, 2),
            ],
            'available_tickets' => events_event_ticket_options($event),
        ];
    }
}

if (!function_exists('events_compute_sales')) {
    function events_compute_sales(array $events, array $orders): array
    {
        $salesByEvent = [];
        foreach ($events as $event) {
            $eventId = (string) ($event['id'] ?? '');
            if ($eventId === '') {
                continue;
            }
            $salesByEvent[$eventId] = [
                'tickets_sold' => 0,
                'revenue' => 0.0,
                'refunded' => 0.0,
            ];
        }
        foreach ($orders as $order) {
            $eventId = (string) ($order['event_id'] ?? '');
            if ($eventId === '' || !isset($salesByEvent[$eventId])) {
                continue;
            }
            $quantity = 0;
            $amount = (float) ($order['amount'] ?? 0);
            foreach (($order['tickets'] ?? []) as $ticket) {
                $quantity += max(0, (int) ($ticket['quantity'] ?? 0));
            }
            $status = strtolower((string) ($order['status'] ?? 'paid'));
            if ($status === 'refunded') {
                $salesByEvent[$eventId]['refunded'] += $amount;
            } else {
                $salesByEvent[$eventId]['tickets_sold'] += $quantity;
                $salesByEvent[$eventId]['revenue'] += $amount;
            }
        }

        return $salesByEvent;
    }
}

if (!function_exists('events_recurrence_interval_seconds')) {
    function events_recurrence_interval_seconds(array $recurrence): int
    {
        $interval = max(1, (int) ($recurrence['interval'] ?? 1));
        $unit = $recurrence['unit'] ?? 'days';
        $daySeconds = 86400;
        if ($unit === 'weeks') {
            return $interval * 7 * $daySeconds;
        }
        return $interval * $daySeconds;
    }
}

if (!function_exists('events_event_occurrences')) {
    function events_event_occurrences(array $event, int $fromTimestamp, int $limit = 5): array
    {
        $occurrences = [];
        $fromTimestamp = max(0, $fromTimestamp);
        $startValue = (string) ($event['start'] ?? '');
        if ($startValue === '') {
            return $occurrences;
        }

        $startTimestamp = strtotime($startValue);
        if ($startTimestamp === false) {
            return $occurrences;
        }

        $endValue = (string) ($event['end'] ?? '');
        $endTimestamp = $endValue !== '' ? strtotime($endValue) : false;
        $duration = $endTimestamp !== false && $endTimestamp >= $startTimestamp
            ? $endTimestamp - $startTimestamp
            : null;

        $recurrence = events_normalize_recurrence($event['recurrence'] ?? []);
        if ($recurrence['frequency'] === 'none') {
            if ($startTimestamp >= $fromTimestamp) {
                $occurrences[] = [
                    'index' => 0,
                    'start' => gmdate('Y-m-d\TH:i', $startTimestamp),
                    'end' => $duration !== null ? gmdate('Y-m-d\TH:i', $startTimestamp + $duration) : '',
                ];
            }
            return $occurrences;
        }

        $intervalSeconds = events_recurrence_interval_seconds($recurrence);
        if ($intervalSeconds <= 0) {
            return $occurrences;
        }

        $maxLimit = max(1, (int) $limit);
        $maxCount = null;
        if ($recurrence['end_type'] === 'after' && $recurrence['end_count'] > 0) {
            $maxCount = (int) $recurrence['end_count'];
        }

        $endDateLimit = null;
        if ($recurrence['end_type'] === 'on_date' && $recurrence['end_date'] !== '') {
            $endDateLimit = strtotime($recurrence['end_date'] . ' 23:59:59');
            if ($endDateLimit === false) {
                $endDateLimit = null;
            }
        }

        $index = 0;
        $currentStart = $startTimestamp;

        if ($fromTimestamp > $startTimestamp) {
            $diff = $fromTimestamp - $startTimestamp;
            $steps = (int) floor($diff / $intervalSeconds);
            $candidate = $startTimestamp + ($steps * $intervalSeconds);
            if ($candidate < $fromTimestamp) {
                $steps++;
                $candidate = $startTimestamp + ($steps * $intervalSeconds);
            }
            $index = $steps;
            $currentStart = $candidate;
        }

        while (count($occurrences) < $maxLimit) {
            if ($maxCount !== null && $index >= $maxCount) {
                break;
            }
            if ($endDateLimit !== null && $currentStart > $endDateLimit) {
                break;
            }
            if ($currentStart >= $fromTimestamp) {
                $occurrences[] = [
                    'index' => $index,
                    'start' => gmdate('Y-m-d\TH:i', $currentStart),
                    'end' => $duration !== null ? gmdate('Y-m-d\TH:i', $currentStart + $duration) : '',
                ];
            }
            $index++;
            $currentStart += $intervalSeconds;
            if ($index > 1000) {
                break;
            }
        }

        return $occurrences;
    }
}

if (!function_exists('events_event_next_occurrence')) {
    function events_event_next_occurrence(array $event, ?int $fromTimestamp = null): ?array
    {
        $fromTimestamp = $fromTimestamp ?? time();
        $occurrences = events_event_occurrences($event, $fromTimestamp, 1);
        return $occurrences[0] ?? null;
    }
}

if (!function_exists('events_filter_upcoming')) {
    function events_filter_upcoming(array $events): array
    {
        $now = time();
        $upcoming = [];
        foreach ($events as $event) {
            if (!is_array($event)) {
                continue;
            }
            $recurrence = events_normalize_recurrence($event['recurrence'] ?? []);
            $occurrences = events_event_occurrences($event, $now, 5);
            foreach ($occurrences as $occurrence) {
                $clone = $event;
                $clone['start'] = $occurrence['start'];
                if ($occurrence['end'] !== '') {
                    $clone['end'] = $occurrence['end'];
                } else {
                    unset($clone['end']);
                }
                $clone['recurrence'] = $recurrence;
                if (!isset($clone['recurrence_summary'])) {
                    $clone['recurrence_summary'] = events_recurrence_summary(['recurrence' => $recurrence]);
                }
                $clone['occurrence'] = [
                    'series_id' => (string) ($event['id'] ?? ''),
                    'index' => $occurrence['index'],
                    'start' => $occurrence['start'],
                    'end' => $occurrence['end'],
                    'is_recurring' => $recurrence['frequency'] !== 'none',
                ];
                $upcoming[] = $clone;
            }
        }
        usort($upcoming, static function ($a, $b) {
            $aTime = isset($a['start']) ? strtotime((string) $a['start']) : 0;
            $bTime = isset($b['start']) ? strtotime((string) $b['start']) : 0;
            if ($aTime === $bTime) {
                return strcmp((string) ($a['title'] ?? ''), (string) ($b['title'] ?? ''));
            }
            return $aTime <=> $bTime;
        });
        return array_slice(array_values($upcoming), 0, 50);
    }
}

if (!function_exists('events_format_currency')) {
    function events_format_currency(float $value): string
    {
        return '$' . number_format($value, 2);
    }
}

if (!function_exists('events_default_roles')) {
    function events_default_roles(): array
    {
        return [
            [
                'role' => 'Admin',
                'description' => 'Full access to events, tickets, orders, and settings.',
            ],
            [
                'role' => 'Event Manager',
                'description' => 'Create and manage events, update tickets, and view sales.',
            ],
            [
                'role' => 'Viewer',
                'description' => 'Read-only access to dashboards and reports.',
            ],
        ];
    }
}
