<?php
// File: forms/confirmation_email_template.php
if (!function_exists('build_absolute_asset_url')) {
    function build_absolute_asset_url(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if ($host === '') {
            return $path;
        }
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        if ($path[0] !== '/') {
            $path = '/' . ltrim($path, '/');
        }
        return $scheme . '://' . $host . $path;
    }
}

if (!function_exists('build_confirmation_email_html')) {
    function build_confirmation_email_html(array $settings, array $config): string
    {
        $siteName = sanitize_text((string) ($settings['site_name'] ?? '')) ?: 'Our team';
        $tagline = sanitize_text((string) ($settings['tagline'] ?? ''));
        $logo = isset($settings['logo']) ? (string) $settings['logo'] : '';
        $logoUrl = $logo !== '' ? build_absolute_asset_url($logo) : '';
        $title = sanitize_text((string) ($config['title'] ?? '')) ?: ('Thank you from ' . $siteName);
        $description = isset($config['description']) ? trim((string) $config['description']) : '';
        $subject = sanitize_text((string) ($config['subject'] ?? '')) ?: ('Thanks for contacting ' . $siteName);

        $descriptionHtml = $description !== ''
            ? '<p style="margin:0 0 16px;color:#334155;font-size:16px;line-height:1.5;">' . nl2br(htmlspecialchars($description, ENT_QUOTES, 'UTF-8')) . '</p>'
            : '';

        $taglineHtml = $tagline !== ''
            ? '<p style="margin:0;color:#64748b;font-size:14px;line-height:1.5;">' . htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8') . '</p>'
            : '';

        $socialLinks = [];
        if (!empty($settings['social']) && is_array($settings['social'])) {
            foreach ($settings['social'] as $label => $url) {
                $cleanUrl = sanitize_url((string) $url);
                if ($cleanUrl === '') {
                    continue;
                }
                $linkLabel = ucwords(str_replace(['_', '-'], ' ', (string) $label));
                $socialLinks[] = '<a href="' . htmlspecialchars($cleanUrl, ENT_QUOTES, 'UTF-8') . '" style="color:#6366f1;text-decoration:none;">' . htmlspecialchars($linkLabel, ENT_QUOTES, 'UTF-8') . '</a>';
            }
        }

        $socialHtml = $socialLinks
            ? '<p style="margin:24px 0 0;color:#64748b;font-size:13px;">Connect with us: ' . implode(' · ', $socialLinks) . '</p>'
            : '';

        $logoHtml = $logoUrl !== ''
            ? '<div style="text-align:center;margin-bottom:16px;"><img src="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . ' logo" style="max-width:200px;height:auto;"></div>'
            : '';

        $titleHtml = '<h1 style="margin:0 0 12px;font-size:24px;line-height:1.2;color:#0f172a;">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>';

        return '<!DOCTYPE html>'
            . '<html lang="en">'
            . '<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">'
            . '<title>' . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . '</title></head>'
            . '<body style="margin:0;padding:0;background-color:#f8fafc;font-family:Arial,Helvetica,sans-serif;">'
            . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#f8fafc;padding:24px 0;">'
            . '<tr><td align="center">'
            . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;box-shadow:0 10px 30px rgba(15,23,42,0.08);padding:32px;">'
            . '<tr><td>'
            . $logoHtml
            . $titleHtml
            . $descriptionHtml
            . $taglineHtml
            . $socialHtml
            . '</td></tr></table>'
            . '</td></tr></table>'
            . '</body></html>';
    }
}
