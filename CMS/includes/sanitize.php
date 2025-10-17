<?php
// Simple sanitization helpers
// Returns a trimmed string with tags stripped
function sanitize_text(string $str): string {
    return trim(strip_tags($str));
}
// Sanitizes url
function sanitize_url(string $url): string {
    return filter_var(trim($url), FILTER_SANITIZE_URL) ?: '';
}
// Sanitize HTML datetime-local input values
function sanitize_datetime_local($value): string {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $formats = ['Y-m-d\TH:i', 'Y-m-d\TH:i:s'];
    foreach ($formats as $format) {
        $date = DateTimeImmutable::createFromFormat($format, $value);
        if ($date instanceof DateTimeImmutable) {
            return $date->format('Y-m-d\TH:i');
        }
    }

    return '';
}
// Sanitizes an array of tags by running sanitize_text on each
function sanitize_tags($tags) {
    if (!is_array($tags)) return [];
    return array_values(array_filter(array_map('sanitize_text', $tags)));
}

if (!defined('SPARKCMS_DEFAULT_ROBOTS_DIRECTIVE')) {
    define('SPARKCMS_DEFAULT_ROBOTS_DIRECTIVE', 'index,follow');
}

function sparkcms_default_robots_directive(): string {
    return SPARKCMS_DEFAULT_ROBOTS_DIRECTIVE;
}

function sanitize_robots_directive($value): string {
    $normalized = strtolower(trim((string)$value));
    if ($normalized === '') {
        return sparkcms_default_robots_directive();
    }

    $normalized = str_replace([';', '|'], ',', $normalized);
    $parts = preg_split('/[\s,]+/', $normalized, -1, PREG_SPLIT_NO_EMPTY);

    $indexDirective = 'index';
    $followDirective = 'follow';

    foreach ($parts as $part) {
        if ($part === 'index' || $part === 'noindex') {
            $indexDirective = $part;
        }
        if ($part === 'follow' || $part === 'nofollow') {
            $followDirective = $part;
        }
    }

    $directive = $indexDirective . ',' . $followDirective;
    $allowed = [
        'index,follow',
        'index,nofollow',
        'noindex,follow',
        'noindex,nofollow',
    ];

    if (!in_array($directive, $allowed, true)) {
        return sparkcms_default_robots_directive();
    }

    return $directive;
}
?>
