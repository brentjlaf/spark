<?php
$bodyClass = $bodyClass ?? '';
$faviconSetting = $settings['favicon'] ?? '';
if (is_string($faviconSetting) && $faviconSetting !== '' && preg_match('#^https?://#i', $faviconSetting)) {
    $favicon = $faviconSetting;
} elseif (!empty($settings['favicon'])) {
    $favicon = $scriptBase . '/CMS/' . ltrim($settings['favicon'], '/');
} else {
    $favicon = $themeBase . '/images/favicon.png';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Metas & Morweb CMS Assets -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Favicon -->
        <link rel="shortcut icon" href="<?php echo htmlspecialchars($favicon); ?>" type="image/x-icon"/>

        <!-- Theme Styles -->
        <link rel="stylesheet" href="<?php echo htmlspecialchars($themeBase); ?>/css/root.css">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($themeBase); ?>/css/skin.css">
        <link rel="stylesheet" href="<?php echo htmlspecialchars($themeBase); ?>/css/override.css">
    </head>
    <body<?php echo $bodyClass !== '' ? ' class="' . htmlspecialchars($bodyClass) . '"' : ''; ?>>
