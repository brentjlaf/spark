<?php
// Shared navigation helper functions used across page templates.

if (!function_exists('resolveMenuUrl')) {
    function resolveMenuUrl(array $item, $scriptBase)
    {
        $type = $item['type'] ?? '';
        $slug = isset($item['slug']) ? trim((string) $item['slug']) : '';
        $link = isset($item['link']) ? trim((string) $item['link']) : '';

        if ($type === 'page') {
            if ($slug === '' && $link !== '') {
                $path = parse_url($link, PHP_URL_PATH);
                if (is_string($path)) {
                    $slug = ltrim($path, '/');
                }
            } else {
                $slug = ltrim($slug, '/');
            }

            if ($slug !== '') {
                $base = rtrim((string) $scriptBase, '/');
                if ($base === '' || $base === '/') {
                    return '/' . $slug;
                }
                return $base . '/' . $slug;
            }
        }

        if ($link === '') {
            return '#';
        }

        if (preg_match('#^(?:[a-z][a-z0-9+\-.]*:|//)#i', $link) || $link[0] === '#') {
            return $link;
        }

        if ($link[0] === '/') {
            $base = rtrim((string) $scriptBase, '/');
            if ($base === '' || $base === '/') {
                return $link;
            }
            return $base . $link;
        }

        $base = rtrim((string) $scriptBase, '/');
        if ($base === '' || $base === '/') {
            return '/' . ltrim($link, '/');
        }
        return $base . '/' . ltrim($link, '/');
    }
}

if (!function_exists('renderMenu')) {
    function renderMenu($items, $isDropdown = false)
    {
        global $scriptBase;
        foreach ($items as $it) {
            $hasChildren = !empty($it['children']);
            if ($hasChildren) {
                echo '<li class="nav-item dropdown">';
                $url = resolveMenuUrl($it, $scriptBase);
                echo '<a class="nav-link dropdown-toggle" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . ' role="button" data-bs-toggle="dropdown" aria-expanded="false">' . htmlspecialchars($it['label']) . '</a>';
                echo '<ul class="dropdown-menu">';
                renderMenu($it['children'], true);
                echo '</ul>';
            } else {
                echo '<li class="nav-item' . ($isDropdown ? '' : '') . '">';
                $url = resolveMenuUrl($it, $scriptBase);
                echo '<a class="nav-link" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . '>' . htmlspecialchars($it['label']) . '</a>';
            }
            echo '</li>';
        }
    }
}

if (!function_exists('renderFooterMenu')) {
    function renderFooterMenu($items)
    {
        global $scriptBase;
        foreach ($items as $it) {
            $url = resolveMenuUrl($it, $scriptBase);
            echo '<li class="nav-item">';
            echo '<a class="nav-link text-light px-2" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . '>' . htmlspecialchars($it['label']) . '</a>';
            echo '</li>';
        }
    }
}
