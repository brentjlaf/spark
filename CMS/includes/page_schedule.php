<?php
// File: page_schedule.php
// Helpers for working with page publish scheduling windows.

/**
 * Normalize a timestamp-like value to an integer or null.
 */
function sparkcms_normalize_timestamp($value): ?int {
    if ($value instanceof DateTimeInterface) {
        return $value->getTimestamp();
    }
    if (is_numeric($value)) {
        $int = (int) $value;
        return $int > 0 ? $int : null;
    }
    return null;
}

/**
 * Parse a datetime-local string into a Unix timestamp.
 */
function sparkcms_parse_datetime_local($value): ?int {
    if (!is_string($value)) {
        return sparkcms_normalize_timestamp($value);
    }
    $value = trim($value);
    if ($value === '') {
        return null;
    }

    $timezone = new DateTimeZone(date_default_timezone_get());
    $formats = ['Y-m-d\\TH:i', 'Y-m-d H:i', DateTimeInterface::ATOM, DateTimeInterface::RFC3339_EXTENDED];
    foreach ($formats as $format) {
        $dt = DateTimeImmutable::createFromFormat($format, $value, $timezone);
        if ($dt instanceof DateTimeImmutable) {
            return $dt->getTimestamp();
        }
    }

    $timestamp = strtotime($value);
    return $timestamp !== false ? $timestamp : null;
}

/**
 * Provide normalized scheduling metadata for a page.
 */
function sparkcms_page_schedule(array $page): array {
    $publishAt = sparkcms_normalize_timestamp($page['publish_at'] ?? null);
    $unpublishAt = sparkcms_normalize_timestamp($page['unpublish_at'] ?? null);

    if ($publishAt !== null && $publishAt <= 0) {
        $publishAt = null;
    }
    if ($unpublishAt !== null && $unpublishAt <= 0) {
        $unpublishAt = null;
    }

    return [
        'publish_at' => $publishAt,
        'unpublish_at' => $unpublishAt,
    ];
}

/**
 * Determine scheduling state for a page.
 */
function sparkcms_evaluate_page_publication(array $page, ?int $now = null): array {
    $now = $now ?? time();
    $schedule = sparkcms_page_schedule($page);
    $isEnabled = !empty($page['published']);
    $publishAt = $schedule['publish_at'];
    $unpublishAt = $schedule['unpublish_at'];

    $status = 'draft';
    if ($isEnabled) {
        if ($publishAt !== null && $now < $publishAt) {
            $status = 'scheduled';
        } elseif ($unpublishAt !== null && $now >= $unpublishAt) {
            $status = 'expired';
        } else {
            $status = 'published';
        }
    }

    return [
        'status' => $status,
        'is_currently_published' => $status === 'published',
        'publish_at' => $publishAt,
        'unpublish_at' => $unpublishAt,
        'raw_published' => $isEnabled,
    ];
}

/**
 * Convenience helper mirroring evaluate result.
 */
function sparkcms_is_page_currently_published(array $page, ?int $now = null): bool {
    $evaluation = sparkcms_evaluate_page_publication($page, $now);
    return $evaluation['is_currently_published'];
}
