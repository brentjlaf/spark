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
                echo '<li class="relative group">';
                $url = resolveMenuUrl($it, $scriptBase);
                echo '<a class="inline-flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-primary-600 transition" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . ' aria-haspopup="true" aria-expanded="false">';
                echo htmlspecialchars($it['label']);
                echo '<i class="fa-solid fa-chevron-down text-xs"></i>';
                echo '</a>';
                echo '<ul class="absolute left-0 mt-3 hidden min-w-[12rem] rounded-xl border border-slate-200 bg-white p-2 shadow-soft group-hover:block">';
                renderMenu($it['children'], true);
                echo '</ul>';
            } else {
                echo '<li class="' . ($isDropdown ? '' : '') . '">';
                $url = resolveMenuUrl($it, $scriptBase);
                $linkClasses = $isDropdown
                    ? 'flex w-full items-center rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-slate-900'
                    : 'text-sm font-medium text-slate-700 hover:text-primary-600 transition';
                echo '<a class="' . $linkClasses . '" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . '>' . htmlspecialchars($it['label']) . '</a>';
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
            echo '<li>';
            echo '<a class="text-sm text-slate-300 hover:text-white transition" href="' . htmlspecialchars($url) . '"' . (!empty($it['new_tab']) ? ' target="_blank"' : '') . '>' . htmlspecialchars($it['label']) . '</a>';
            echo '</li>';
        }
    }
}
