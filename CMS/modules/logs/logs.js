// File: logs.js
$(function(){
    const dashboard = $('.logs-dashboard');
    if (!dashboard.length) {
        return;
    }

    const timeline = $('#logsTimeline');
    const matchCountEl = $('#logsMatchCount');
    const filterContainer = $('#logsFilters');
    const userFilter = $('#logsUserFilter');
    const refreshBtn = $('#logsRefreshBtn');
    const endpoint = dashboard.data('endpoint');

    const statsEls = {
        total: $('#logsTotalCount'),
        last7: $('#logsLast7Days'),
        users: $('#logsUserCount'),
        pages: $('#logsPageCount'),
        topActionLabel: $('#logsTopActionLabel'),
        topActionCount: $('#logsTopActionCount'),
        lastActivity: $('#logsLastActivity'),
        past24h: $('#logsPast24h')
    };

    let currentAction = 'all';
    let currentUser = 'all';
    let allLogs = [];

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }

    function getActionLabel(log) {
        const raw = log && typeof log.action !== 'undefined' ? String(log.action) : '';
        const label = raw.trim();
        return label !== '' ? label : 'Updated content';
    }

    function slugifyAction(label) {
        return label.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '') || 'unknown';
    }

    function normalizeUserValue(value) {
        if (value === null || typeof value === 'undefined') {
            return '';
        }
        return String(value).trim();
    }

    function userKeyFromNormalized(normalized) {
        return normalized === '' ? 'system' : normalized.toLowerCase();
    }

    function userDisplayFromNormalized(normalized) {
        return normalized === '' ? 'System' : normalized;
    }

    function formatAbsolute(timestamp) {
        if (!timestamp) {
            return 'No recent activity';
        }
        const date = new Date(timestamp * 1000);
        return date.toLocaleString();
    }

    function heroTime(timestamp) {
        if (!timestamp) {
            return 'No activity yet';
        }
        const now = Date.now();
        const diff = now - timestamp * 1000;
        if (diff < 0) {
            return 'Scheduled update';
        }
        const seconds = Math.floor(diff / 1000);
        if (seconds < 60) {
            return 'Just now';
        }
        if (seconds < 3600) {
            const minutes = Math.floor(seconds / 60);
            return minutes + ' min' + (minutes === 1 ? '' : 's') + ' ago';
        }
        if (seconds < 86400) {
            const hours = Math.floor(seconds / 3600);
            return hours + ' hour' + (hours === 1 ? '' : 's') + ' ago';
        }
        if (seconds < 604800) {
            const days = Math.floor(seconds / 86400);
            return days + ' day' + (days === 1 ? '' : 's') + ' ago';
        }
        return formatAbsolute(timestamp);
    }

    function relativeTime(timestamp) {
        if (!timestamp) {
            return 'Unknown time';
        }
        const now = Date.now();
        const diff = now - timestamp * 1000;
        if (diff < 0) {
            return 'Scheduled update';
        }
        const seconds = Math.floor(diff / 1000);
        if (seconds < 60) {
            return 'Just now';
        }
        if (seconds < 3600) {
            const minutes = Math.floor(seconds / 60);
            return minutes + ' min' + (minutes === 1 ? '' : 's');
        }
        if (seconds < 86400) {
            const hours = Math.floor(seconds / 3600);
            return hours + ' hr' + (hours === 1 ? '' : 's');
        }
        if (seconds < 604800) {
            const days = Math.floor(seconds / 86400);
            return days + ' day' + (days === 1 ? '' : 's');
        }
        return formatAbsolute(timestamp);
    }

    function normalizeLogs(logs) {
        if (typeof logs === 'string') {
            try {
                logs = JSON.parse(logs);
            } catch (err) {
                logs = [];
            }
        }
        if (!Array.isArray(logs)) {
            return [];
        }
        return logs.map(function(item){
            const label = getActionLabel(item);
            const slug = item && item.action_slug ? String(item.action_slug) : slugifyAction(label);
            const context = item && item.context ? String(item.context) : 'page';
            const rawDetails = item && typeof item.details !== 'undefined' ? item.details : [];
            let details = [];
            if (Array.isArray(rawDetails)) {
                details = rawDetails.map(function(detail){
                    return String(detail);
                });
            } else if (rawDetails !== null && rawDetails !== '' && typeof rawDetails !== 'undefined') {
                details = [String(rawDetails)];
            }
            const normalizedUser = normalizeUserValue(item && item.user);
            const userKey = userKeyFromNormalized(normalizedUser);
            const userDisplay = userDisplayFromNormalized(normalizedUser);

            return {
                time: parseInt(item.time, 10) || 0,
                user: normalizedUser,
                user_display: userDisplay,
                user_key: userKey,
                page_title: item && item.page_title ? String(item.page_title) : (context === 'system' ? 'System activity' : 'Unknown'),
                action: label,
                action_slug: slug,
                context: context,
                details: details
            };
        }).sort(function(a, b){
            return b.time - a.time;
        });
    }

    function summarizeActions(logs) {
        const summary = {};
        logs.forEach(function(log){
            const label = getActionLabel(log);
            const slug = log.action_slug || slugifyAction(label);
            if (!summary[slug]) {
                summary[slug] = { slug: slug, label: label, count: 0 };
            }
            summary[slug].count += 1;
        });
        return Object.values(summary).sort(function(a, b){
            return b.count - a.count;
        });
    }

    function renderFilters(logs) {
        if (!filterContainer.length) {
            return;
        }

        logs = Array.isArray(logs) ? logs : [];

        const actions = summarizeActions(logs);
        if (currentAction !== 'all' && !actions.some(function(action){ return action.slug === currentAction; })) {
            currentAction = 'all';
        }

        filterContainer.empty();

        const allBtn = $('<button type="button" class="logs-filter-btn"></button>')
            .attr('data-filter', 'all')
            .toggleClass('active', currentAction === 'all')
            .append($('<span>').text('All activity'))
            .append($('<span class="logs-filter-count" id="logsAllCount"></span>').text(logs.length));

        filterContainer.append(allBtn);

        actions.slice(0, 4).forEach(function(action){
            const btn = $('<button type="button" class="logs-filter-btn"></button>')
                .attr('data-filter', action.slug)
                .toggleClass('active', currentAction === action.slug)
                .append($('<span>').text(action.label))
                .append($('<span class="logs-filter-count"></span>').attr('data-filter-count', action.slug).text(action.count));
            filterContainer.append(btn);
        });
    }

    function buildRow(log) {
        const label = getActionLabel(log);
        const slug = log.action_slug || slugifyAction(label);
        const timestamp = log.time || 0;
        const exact = timestamp ? new Date(timestamp * 1000).toISOString() : '';
        const absolute = formatAbsolute(timestamp);
        const relative = relativeTime(timestamp);
        const context = log.context || 'page';
        const pageTitle = log.page_title || (context === 'system' ? 'System activity' : 'Unknown');
        const userDisplay = log.user_display || 'System';
        const details = Array.isArray(log.details) ? log.details : (log.details ? [log.details] : []);
        const detailsHtml = details.length ? '<ul class="logs-activity-details">' + details.map(function(detail){
            return '<li>' + escapeHtml(detail) + '</li>';
        }).join('') + '</ul>' : '<span class="logs-activity-details-empty">—</span>';
        const searchText = (userDisplay + ' ' + pageTitle + ' ' + label + ' ' + details.join(' ')).toLowerCase();

        return (
            '<tr class="logs-activity-row" data-search="' + escapeHtml(searchText) + '" data-action="' + escapeHtml(slug) + '">' +
                '<td class="logs-activity-cell logs-activity-cell--action" data-label="Action">' +
                    '<span class="logs-activity-badge">' + escapeHtml(label) + '</span>' +
                '</td>' +
                '<td class="logs-activity-cell logs-activity-cell--page" data-label="Page">' +
                    '<span class="logs-activity-page">' + escapeHtml(pageTitle) + '</span>' +
                '</td>' +
                '<td class="logs-activity-cell logs-activity-cell--user" data-label="Editor">' +
                    '<span class="logs-activity-user">' + escapeHtml(userDisplay) + '</span>' +
                '</td>' +
                '<td class="logs-activity-cell logs-activity-cell--details" data-label="Details">' +
                    detailsHtml +
                '</td>' +
                '<td class="logs-activity-cell logs-activity-cell--time" data-label="When">' +
                    '<time datetime="' + exact + '" class="logs-activity-time" title="' + escapeHtml(absolute) + '">' +
                        escapeHtml(relative) +
                    '</time>' +
                '</td>' +
            '</tr>'
        );
    }

    function updateTimeline(logs) {
        if (!logs.length) {
            const hasAnyLogs = allLogs.length > 0;
            const isFiltered = currentAction !== 'all' || currentUser !== 'all';
            const message = hasAnyLogs && isFiltered ? 'No activity matches the current filters.' : 'No activity recorded yet.';
            const hint = hasAnyLogs && isFiltered ? 'Try selecting a different editor or action.' : 'Updates will appear here as your team edits content.';
            timeline.html('<div class="logs-empty"><i class="fas fa-clipboard-list" aria-hidden="true"></i><p>' + escapeHtml(message) + '</p><p class="logs-empty-hint">' + escapeHtml(hint) + '</p></div>');
            return;
        }
        const rows = logs.map(buildRow).join('');
        const table = '<div class="logs-activity-table-scroll">' +
            '<table class="logs-activity-table">' +
                '<thead>' +
                    '<tr>' +
                        '<th scope="col">Action</th>' +
                        '<th scope="col">Page</th>' +
                        '<th scope="col">Editor</th>' +
                        '<th scope="col">Details</th>' +
                        '<th scope="col">When</th>' +
                    '</tr>' +
                '</thead>' +
                '<tbody>' + rows + '</tbody>' +
            '</table>' +
        '</div>';
        timeline.html(table);
    }

    function updateMatchCount(count) {
        if (!matchCountEl.length) {
            return;
        }
        if (count === 0) {
            matchCountEl.text('No entries to display');
            return;
        }
        if (count === 1) {
            matchCountEl.text('1 entry');
            return;
        }
        matchCountEl.text(count + ' entries');
    }

    function updateStats(logs) {
        const total = logs.length;
        const now = Date.now();
        const past24h = logs.filter(function(log){
            return log.time && (now - log.time * 1000) <= 86400000;
        }).length;
        const last7 = logs.filter(function(log){
            return log.time && (now - log.time * 1000) <= 604800000;
        }).length;

        const uniqueUsers = new Set();
        const uniquePages = new Set();
        logs.forEach(function(log){
            if (log.user) {
                uniqueUsers.add(log.user.toLowerCase());
            }
            if (log.page_title) {
                uniquePages.add(log.page_title.toLowerCase());
            }
        });

        const heroTimestamp = logs.length ? logs[0].time : 0;
        const heroLabel = heroTime(heroTimestamp);
        const heroTitle = heroTimestamp ? formatAbsolute(heroTimestamp) : 'No recent activity';

        if (statsEls.total.length) {
            statsEls.total.text(total);
        }
        if (statsEls.last7.length) {
            statsEls.last7.text(last7);
        }
        if (statsEls.users.length) {
            statsEls.users.text(uniqueUsers.size);
        }
        if (statsEls.pages.length) {
            statsEls.pages.text(uniquePages.size);
        }
        if (statsEls.past24h.length) {
            statsEls.past24h.text(past24h);
        }
        if (statsEls.lastActivity.length) {
            statsEls.lastActivity.text(heroLabel).attr('title', heroTitle);
        }
        const allCountEl = $('#logsAllCount');
        if (allCountEl.length) {
            allCountEl.text(total);
        }

        const actions = summarizeActions(logs);
        if (statsEls.topActionLabel.length && statsEls.topActionCount.length) {
            if (actions.length) {
                statsEls.topActionLabel.text(actions[0].label);
                statsEls.topActionCount.text(actions[0].count + (actions[0].count === 1 ? ' entry' : ' entries'));
            } else {
                statsEls.topActionLabel.text('—');
                statsEls.topActionCount.text('No recorded actions yet');
            }
        }
    }

    function logsForCurrentUser() {
        if (currentUser === 'all') {
            return allLogs;
        }
        return allLogs.filter(function(log){
            return log.user_key === currentUser;
        });
    }

    function applyFilters() {
        const userFiltered = logsForCurrentUser();
        const filtered = userFiltered.filter(function(log){
            const slug = log.action_slug || slugifyAction(getActionLabel(log));
            return currentAction === 'all' || slug === currentAction;
        });

        updateTimeline(filtered);
        updateMatchCount(filtered.length);
    }

    function renderUserFilter(logs) {
        if (!userFilter.length) {
            return;
        }

        const userMap = new Map();
        logs.forEach(function(log){
            const key = log.user_key || userKeyFromNormalized(normalizeUserValue(log.user));
            const label = log.user_display || userDisplayFromNormalized(normalizeUserValue(log.user));
            if (!userMap.has(key)) {
                userMap.set(key, label);
            }
        });

        if (currentUser !== 'all' && !userMap.has(currentUser)) {
            currentUser = 'all';
        }

        const sortedUsers = Array.from(userMap.entries()).sort(function(a, b){
            if (a[0] === 'system') {
                return -1;
            }
            if (b[0] === 'system') {
                return 1;
            }
            return a[1].toLowerCase().localeCompare(b[1].toLowerCase());
        });

        if (!sortedUsers.length) {
            currentUser = 'all';
        }

        userFilter.empty();
        userFilter.append($('<option value="all">All editors</option>'));
        sortedUsers.forEach(function(entry){
            userFilter.append($('<option></option>').val(entry[0]).text(entry[1]));
        });

        userFilter.prop('disabled', sortedUsers.length === 0);
        userFilter.val(currentUser);
    }

    function setLogs(logs) {
        allLogs = normalizeLogs(logs);
        updateStats(allLogs);
        renderUserFilter(allLogs);
        renderFilters(logsForCurrentUser());
        applyFilters();
    }

    filterContainer.on('click', 'button', function(){
        const filter = $(this).data('filter') || 'all';
        if (currentAction === filter) {
            return;
        }
        currentAction = filter;
        filterContainer.find('button').removeClass('active');
        $(this).addClass('active');
        applyFilters();
    });

    if (userFilter.length) {
        userFilter.on('change', function(){
            const selected = $(this).val();
            currentUser = selected && selected !== 'all' ? String(selected) : 'all';
            renderFilters(logsForCurrentUser());
            applyFilters();
        });
    }

    if (refreshBtn.length) {
        refreshBtn.on('click', function(){
            if (!endpoint) {
                return;
            }
            refreshBtn.prop('disabled', true).addClass('is-loading');
            $.getJSON(endpoint).done(function(data){
                setLogs(data || []);
            }).fail(function(){
                const alert = $('<div class="logs-inline-alert" role="status">Unable to refresh activity right now.</div>');
                timeline.prepend(alert);
                setTimeout(function(){
                    alert.fadeOut(250, function(){
                        $(this).remove();
                    });
                }, 4000);
            }).always(function(){
                refreshBtn.prop('disabled', false).removeClass('is-loading');
            });
        });
    }

    setLogs(dashboard.data('logs'));
});
