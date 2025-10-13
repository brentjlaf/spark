/* File: modules/events/events.js */
(function () {
    const root = document.querySelector('.events-dashboard');
    if (!root) {
        return;
    }

    const endpoint = root.dataset.eventsEndpoint || '';
    const initialPayload = (() => {
        try {
            return JSON.parse(root.dataset.eventsInitial || '{}');
        } catch (error) {
            return {};
        }
    })();

    const selectors = {
        stats: {
            events: root.querySelector('[data-events-stat="events"]'),
            tickets: root.querySelector('[data-events-stat="tickets"]'),
            revenue: root.querySelector('[data-events-stat="revenue"]'),
        },
        upcoming: {
            list: root.querySelector('[data-events-upcoming-list]'),
            viewButtons: Array.from(root.querySelectorAll('[data-events-upcoming-view]')),
            panels: Array.from(root.querySelectorAll('[data-events-upcoming-panel]')),
            calendar: {
                container: root.querySelector('[data-events-calendar]'),
                label: root.querySelector('[data-events-calendar-label]'),
                grid: root.querySelector('[data-events-calendar-grid]'),
                prev: root.querySelector('[data-events-calendar-nav="prev"]'),
                next: root.querySelector('[data-events-calendar-nav="next"]'),
                empty: root.querySelector('[data-events-calendar-empty]'),
            },
        },
        tableBody: root.querySelector('[data-events-table]'),
        filters: {
            status: root.querySelector('[data-events-filter="status"]'),
            search: root.querySelector('[data-events-filter="search"]'),
        },
        orders: {
            body: root.querySelector('[data-events-orders]'),
            filterEvent: root.querySelector('[data-events-orders-filter="event"]'),
            filterStatus: root.querySelector('[data-events-orders-filter="status"]'),
            exportBtn: root.querySelector('[data-events-export]'),
        },
        reports: {
            tableBody: root.querySelector('[data-events-reports-table]'),
            metrics: {
                container: root.querySelector('[data-events-report-metrics]'),
                revenue: root.querySelector('[data-events-report-metric="revenue"]'),
                averageOrder: root.querySelector('[data-events-report-metric="average_order"]'),
                refunds: root.querySelector('[data-events-report-metric="refunds"]'),
            },
            insights: root.querySelector('[data-events-insights]'),
            downloads: root.querySelector('[data-events-downloads]'),
        },
        tabs: {
            root: root.querySelector('[data-events-tabs]'),
            buttons: Array.from(root.querySelectorAll('[data-events-tab]')),
        },
        orderEditor: {
            modal: document.querySelector('[data-events-modal="order"]'),
            form: document.querySelector('[data-events-form="order"]'),
            title: document.querySelector('[data-events-order-title]'),
            event: document.querySelector('[data-events-order-event]'),
            status: document.querySelector('[data-events-order-status]'),
            lines: document.querySelector('[data-events-order-lines]'),
            summary: document.querySelector('[data-events-order-summary]'),
            totals: {
                subtotal: document.querySelector('[data-order-total="subtotal"]'),
                refunds: document.querySelector('[data-order-total="refunds"]'),
                net: document.querySelector('[data-order-total="net"]'),
            },
            breakdown: document.querySelector('[data-events-order-breakdown]'),
            addSelect: document.querySelector('[data-events-order-add-select]'),
            addButton: document.querySelector('[data-events-order-add]'),
        },
        modal: document.querySelector('[data-events-modal="event"]'),
        confirmModal: document.querySelector('[data-events-modal="confirm"]'),
        categoriesModal: document.querySelector('[data-events-modal="categories"]'),
        mediaModal: document.querySelector('[data-events-modal="media"]'),
        mediaGrid: document.querySelector('[data-events-media-grid]'),
        mediaSearch: document.querySelector('[data-events-media-search]'),
        categoriesForm: document.querySelector('[data-events-form="category"]'),
        categoriesList: document.querySelector('[data-events-categories-list]'),
        categoriesFormTitle: document.querySelector('[data-events-category-form-title]'),
        categoriesSubmit: document.querySelector('[data-events-category-submit]'),
        categoriesReset: document.querySelector('[data-events-category-reset]'),
        toast: document.querySelector('[data-events-toast]'),
    };

    const state = {
        events: new Map(),
        eventRows: [],
        orders: [],
        salesSummary: [],
        categories: [],
        upcoming: [],
        upcomingView: 'list',
        calendar: {
            currentMonth: null,
        },
        filters: {
            status: '',
            search: '',
        },
        ordersFilter: {
            event: '',
            status: '',
        },
        confirm: null,
        categoryEditing: null,
        media: {
            items: [],
            loaded: false,
            loading: false,
            currentSetter: null,
        },
        orderEditor: {
            detail: null,
        },
    };

    if (Array.isArray(initialPayload.events)) {
        initialPayload.events.forEach((event) => {
            if (event && event.id) {
                state.events.set(String(event.id), event);
            }
        });
    }
    if (Array.isArray(initialPayload.categories)) {
        state.categories = sortCategories(initialPayload.categories);
    }
    if (initialPayload.sales && typeof initialPayload.sales === 'object') {
        state.salesSummary = Object.entries(initialPayload.sales).map(([eventId, metrics]) => ({
            event_id: eventId,
            title: state.events.get(String(eventId))?.title || 'Event',
            tickets_sold: metrics.tickets_sold ?? 0,
            revenue: metrics.revenue ?? 0,
            refunded: metrics.refunded ?? 0,
            status: state.events.get(String(eventId))?.status || 'draft',
        }));
    }
    if (Array.isArray(initialPayload.orders)) {
        state.orders = initialPayload.orders
            .map((order) => normalizeOrderRow(order))
            .filter((order) => order !== null);
    }

    if (Array.isArray(initialPayload.upcoming)) {
        state.upcoming = initialPayload.upcoming
            .map((item) => normalizeUpcomingItem(item))
            .filter((item) => item !== null);
    }

    initializeTabs();
    initializeUpcoming();
    renderUpcoming(state.upcoming);

    function initializeTabs() {
        const tabButtons = selectors.tabs.buttons;
        const tabPanels = Array.from(root.querySelectorAll('[data-events-panel]'));

        if (!selectors.tabs.root || tabButtons.length === 0 || tabPanels.length === 0) {
            return;
        }

        selectors.tabs.root.setAttribute('role', 'tablist');
        selectors.tabs.root.setAttribute('aria-orientation', 'horizontal');

        let activeId =
            tabButtons.find((button) => button.classList.contains('is-active'))?.dataset.eventsTab ||
            tabButtons[0].dataset.eventsTab;

        function activate(tabId, options = {}) {
            const tab = tabButtons.find((button) => button.dataset.eventsTab === tabId);
            const panel = tabPanels.find((section) => section.dataset.eventsPanel === tabId);

            if (!tab || !panel) {
                return;
            }

            activeId = tabId;

            tabButtons.forEach((button) => {
                const isActive = button === tab;
                button.classList.toggle('is-active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
                button.setAttribute('tabindex', isActive ? '0' : '-1');
            });

            tabPanels.forEach((section) => {
                const isActive = section === panel;
                section.classList.toggle('is-active', isActive);
                section.hidden = !isActive;
            });

            if (options.focus) {
                tab.focus();
            }
        }

        function focusByIndex(index) {
            const normalizedIndex = (index + tabButtons.length) % tabButtons.length;
            const target = tabButtons[normalizedIndex];
            if (target) {
                activate(target.dataset.eventsTab, { focus: true });
            }
        }

        selectors.tabs.root.addEventListener('keydown', (event) => {
            const target = event.target instanceof HTMLElement ? event.target.closest('[data-events-tab]') : null;
            if (!target) {
                return;
            }

            const currentIndex = tabButtons.findIndex((button) => button === target);
            if (currentIndex === -1) {
                return;
            }

            switch (event.key) {
                case 'ArrowRight':
                case 'ArrowDown':
                    event.preventDefault();
                    focusByIndex(currentIndex + 1);
                    break;
                case 'ArrowLeft':
                case 'ArrowUp':
                    event.preventDefault();
                    focusByIndex(currentIndex - 1);
                    break;
                case 'Home':
                    event.preventDefault();
                    focusByIndex(0);
                    break;
                case 'End':
                    event.preventDefault();
                    focusByIndex(tabButtons.length - 1);
                    break;
                default:
                    break;
            }
        });

        tabButtons.forEach((button) => {
            if (!button.dataset.eventsTab) {
                return;
            }

            button.addEventListener('click', () => {
                activate(button.dataset.eventsTab, { focus: true });
            });

            button.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                    event.preventDefault();
                    activate(button.dataset.eventsTab, { focus: true });
                }
            });
        });

        root.classList.add('events-dashboard--tabs-ready');
        tabPanels.forEach((section) => {
            section.hidden = !section.classList.contains('is-active');
        });
        activate(activeId);
    }

    function initializeUpcoming() {
        const upcomingSelectors = selectors.upcoming;
        if (!upcomingSelectors) {
            return;
        }

        const { viewButtons, panels, calendar } = upcomingSelectors;

        if (Array.isArray(viewButtons) && viewButtons.length > 0 && Array.isArray(panels) && panels.length > 0) {
            setUpcomingView(state.upcomingView);

            viewButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetView = button.dataset.eventsUpcomingView || 'list';
                    if (state.upcomingView !== targetView) {
                        setUpcomingView(targetView);
                    }
                });

                button.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar') {
                        event.preventDefault();
                        const targetView = button.dataset.eventsUpcomingView || 'list';
                        if (state.upcomingView !== targetView) {
                            setUpcomingView(targetView);
                        }
                    }
                });
            });
        }

        if (calendar?.prev) {
            calendar.prev.addEventListener('click', () => {
                changeCalendarMonth(-1);
            });
        }

        if (calendar?.next) {
            calendar.next.addEventListener('click', () => {
                changeCalendarMonth(1);
            });
        }
    }

    function setUpcomingView(view) {
        const upcomingSelectors = selectors.upcoming;
        if (!upcomingSelectors) {
            state.upcomingView = view;
            return;
        }

        const { viewButtons = [], panels = [] } = upcomingSelectors;
        state.upcomingView = view;

        viewButtons.forEach((button) => {
            const isActive = (button.dataset.eventsUpcomingView || 'list') === view;
            button.classList.toggle('is-active', isActive);
            button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });

        panels.forEach((panel) => {
            const isActive = (panel.dataset.eventsUpcomingPanel || 'list') === view;
            panel.classList.toggle('is-active', isActive);
            panel.hidden = !isActive;
        });

        if (view === 'calendar') {
            renderUpcomingCalendar();
        }
    }

    function changeCalendarMonth(offset) {
        const todayMonth = startOfMonth(new Date());
        const currentMonth = state.calendar.currentMonth ? startOfMonth(state.calendar.currentMonth) : todayMonth;
        let target = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + offset, 1);
        const latestMonth = getLatestEventMonth();

        if (offset < 0 && target < todayMonth) {
            target = todayMonth;
        }

        if (offset > 0 && latestMonth && target > latestMonth) {
            target = startOfMonth(latestMonth);
        }

        if (offset > 0 && !latestMonth) {
            target = todayMonth;
        }

        state.calendar.currentMonth = startOfMonth(target);
        renderUpcomingCalendar();
    }

    function formatDate(value) {
        if (!value) {
            return 'Date TBD';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return 'Date TBD';
        }
        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
        }).format(date);
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2,
        }).format(Number(value || 0));
    }

    function normalizeUpcomingItem(item) {
        if (!item || typeof item !== 'object') {
            return null;
        }

        const occurrence =
            item.occurrence && typeof item.occurrence === 'object'
                ? {
                      series_id: String(item.occurrence.series_id ?? item.id ?? ''),
                      index: Number.parseInt(item.occurrence.index ?? 0, 10) || 0,
                  }
                : null;

        return {
            id: String(item.id ?? ''),
            title: item.title ?? 'Untitled event',
            start: item.start ?? '',
            end: item.end ?? '',
            tickets_sold: Number.parseInt(item.tickets_sold ?? 0, 10) || 0,
            revenue: Number(item.revenue ?? 0) || 0,
            recurrence_summary: typeof item.recurrence_summary === 'string' ? item.recurrence_summary : '',
            occurrence,
        };
    }

    function parseDateValue(value) {
        if (!value) {
            return null;
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return null;
        }
        return date;
    }

    function startOfMonth(date) {
        const reference = date instanceof Date ? date : new Date(date);
        if (Number.isNaN(reference.getTime())) {
            const fallback = new Date();
            return new Date(fallback.getFullYear(), fallback.getMonth(), 1);
        }
        return new Date(reference.getFullYear(), reference.getMonth(), 1);
    }

    function formatMonthLabel(date) {
        if (!(date instanceof Date) || Number.isNaN(date.getTime())) {
            return '';
        }
        return new Intl.DateTimeFormat('en-US', {
            month: 'long',
            year: 'numeric',
        }).format(date);
    }

    function formatTimeLabel(value) {
        const date = parseDateValue(value);
        if (!date) {
            return '';
        }
        return new Intl.DateTimeFormat('en-US', {
            hour: 'numeric',
            minute: '2-digit',
        }).format(date);
    }

    function formatDateKey(date) {
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${date.getFullYear()}-${month}-${day}`;
    }

    function compareUpcomingByStart(a, b) {
        const dateA = parseDateValue(a?.start);
        const dateB = parseDateValue(b?.start);
        if (!dateA && !dateB) {
            return 0;
        }
        if (!dateA) {
            return 1;
        }
        if (!dateB) {
            return -1;
        }
        return dateA.getTime() - dateB.getTime();
    }

    function getEarliestEventMonth(list = state.upcoming) {
        let earliest = null;
        list.forEach((item) => {
            const date = parseDateValue(item.start);
            if (!date) {
                return;
            }
            const month = startOfMonth(date);
            if (!earliest || month.getTime() < earliest.getTime()) {
                earliest = month;
            }
        });
        return earliest;
    }

    function getLatestEventMonth(list = state.upcoming) {
        let latest = null;
        list.forEach((item) => {
            const date = parseDateValue(item.start);
            if (!date) {
                return;
            }
            const month = startOfMonth(date);
            if (!latest || month.getTime() > latest.getTime()) {
                latest = month;
            }
        });
        return latest;
    }

    function isSameMonth(dateA, dateB) {
        if (!(dateA instanceof Date) || !(dateB instanceof Date)) {
            return false;
        }
        return dateA.getFullYear() === dateB.getFullYear() && dateA.getMonth() === dateB.getMonth();
    }

    function toLocalDateTimeInput(value) {
        if (!value) {
            return '';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        const offset = date.getTimezoneOffset();
        const local = new Date(date.getTime() - offset * 60000);
        return local.toISOString().slice(0, 16);
    }

    function fromLocalDateTimeInput(value) {
        if (!value) {
            return '';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        return new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString();
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => {
            switch (character) {
                case '&':
                    return '&amp;';
                case '<':
                    return '&lt;';
                case '>':
                    return '&gt;';
                case '"':
                    return '&quot;';
                case '\'':
                    return '&#39;';
                default:
                    return character;
            }
        });
    }

    function escapeAttribute(value) {
        return escapeHtml(value);
    }

    function sortCategories(list) {
        if (!Array.isArray(list)) {
            return [];
        }
        return list
            .slice()
            .filter((item) => item && item.id && item.name)
            .sort((a, b) => String(a.name || '').localeCompare(String(b.name || ''), undefined, { sensitivity: 'base' }));
    }

    function normalizeOrderRow(order) {
        if (!order || typeof order !== 'object') {
            return null;
        }
        const id = String(order.id || '').trim();
        if (id === '') {
            return null;
        }
        const eventId = String(order.event_id || '').trim();
        const event = eventId ? state.events.get(eventId) : null;
        const ticketLookup = (() => {
            if (!event || !Array.isArray(event.tickets)) {
                return new Map();
            }
            return new Map(
                event.tickets
                    .filter((ticket) => ticket && ticket.id)
                    .map((ticket) => [String(ticket.id), ticket]),
            );
        })();

        let lineItems = [];
        if (Array.isArray(order.line_items) && order.line_items.length > 0) {
            lineItems = order.line_items;
        } else if (Array.isArray(order.tickets)) {
            lineItems = order.tickets.map((ticket) => ({
                ticket_id: ticket.ticket_id,
                quantity: ticket.quantity,
                price: ticket.price,
            }));
        }

        const normalizedLines = [];
        let ticketsTotal = 0;
        let amountTotal = 0;

        lineItems.forEach((item) => {
            if (!item || typeof item !== 'object') {
                return;
            }
            const ticketId = String(item.ticket_id || '').trim();
            if (ticketId === '') {
                return;
            }
            const ticketInfo = ticketLookup.get(ticketId) || {};
            const name = item.name || ticketInfo.name || 'Ticket';
            const price = Math.max(0, Number.parseFloat(item.price ?? ticketInfo.price ?? 0));
            const quantity = Math.max(0, Number.parseInt(item.quantity ?? 0, 10));
            const subtotal = price * quantity;
            ticketsTotal += quantity;
            amountTotal += subtotal;
            normalizedLines.push({
                ticket_id: ticketId,
                name,
                price,
                quantity,
                subtotal,
            });
        });

        const status = String(order.status || 'paid').toLowerCase();
        const orderedAt = order.ordered_at || '';
        const fallbackAmount = Number(order.amount || 0) || 0;
        const computedAmount = Number.isFinite(amountTotal) ? amountTotal : 0;

        return {
            id,
            event_id: eventId,
            event: event ? event.title || 'Untitled event' : String(order.event || ''),
            buyer_name: order.buyer_name || '',
            tickets: ticketsTotal,
            amount: computedAmount || fallbackAmount,
            status,
            ordered_at: orderedAt,
            line_items: normalizedLines,
        };
    }

    function getCategoryOptionsContainer() {
        return selectors.modal?.querySelector('[data-events-category-options]') || null;
    }

    function getSelectedCategoryIds() {
        const container = getCategoryOptionsContainer();
        if (!container) {
            return [];
        }
        return Array.from(container.querySelectorAll('input[name="categories[]"]:checked')).map((input) => input.value);
    }

    function renderCategoryOptions(selectedIds = []) {
        const container = getCategoryOptionsContainer();
        if (!container) {
            return;
        }
        const selectedSet = new Set(Array.isArray(selectedIds) ? selectedIds.map((id) => String(id)) : []);
        container.innerHTML = '';
        if (!Array.isArray(state.categories) || state.categories.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'events-category-empty';
            empty.textContent = 'No categories yet. Manage categories to create one.';
            container.appendChild(empty);
            return;
        }
        state.categories.forEach((category) => {
            const label = document.createElement('label');
            label.className = 'events-category-item';
            label.innerHTML = `
                <input type="checkbox" name="categories[]" value="${category.id}">
                <span>${category.name}</span>
            `;
            const input = label.querySelector('input');
            if (input && selectedSet.has(String(category.id))) {
                input.checked = true;
            }
            container.appendChild(label);
        });
    }

    function renderCategoryList() {
        const list = selectors.categoriesList;
        if (!list) {
            return;
        }
        list.innerHTML = '';
        if (!Array.isArray(state.categories) || state.categories.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.className = 'events-empty';
            cell.textContent = 'No categories yet. Create one above.';
            row.appendChild(cell);
            list.appendChild(row);
            return;
        }
        state.categories.forEach((category) => {
            const row = document.createElement('tr');
            const updatedLabel = category.updated_at ? formatDate(category.updated_at) : '—';
            row.innerHTML = `
                <td>${category.name}</td>
                <td>${category.slug || ''}</td>
                <td>${updatedLabel}</td>
                <td class="events-table-actions">
                    <button type="button" class="events-action" data-events-category-edit data-id="${category.id}">
                        <i class="fa-solid fa-pen"></i><span class="sr-only">Edit</span>
                    </button>
                    <button type="button" class="events-action danger" data-events-category-delete data-id="${category.id}">
                        <i class="fa-solid fa-trash"></i><span class="sr-only">Delete</span>
                    </button>
                </td>
            `;
            list.appendChild(row);
        });
    }

    function updateCategoryFormMode() {
        if (selectors.categoriesFormTitle) {
            selectors.categoriesFormTitle.textContent = state.categoryEditing ? 'Edit category' : 'Create category';
        }
        if (selectors.categoriesSubmit) {
            selectors.categoriesSubmit.textContent = state.categoryEditing ? 'Update category' : 'Save category';
        }
    }

    function resetCategoryForm() {
        if (selectors.categoriesForm) {
            selectors.categoriesForm.reset();
        }
        state.categoryEditing = null;
        updateCategoryFormMode();
    }

    function fillCategoryForm(category) {
        if (!selectors.categoriesForm) {
            return;
        }
        selectors.categoriesForm.querySelector('[name="id"]').value = category?.id || '';
        selectors.categoriesForm.querySelector('[name="name"]').value = category?.name || '';
        selectors.categoriesForm.querySelector('[name="slug"]').value = category?.slug || '';
        state.categoryEditing = category?.id || null;
        updateCategoryFormMode();
    }

    function openCategoriesModal(categoryId = null) {
        if (!selectors.categoriesModal) {
            return;
        }
        renderCategoryList();
        resetCategoryForm();
        if (categoryId) {
            const category = state.categories.find((item) => String(item.id) === String(categoryId));
            if (category) {
                fillCategoryForm(category);
            }
        }
        openModal(selectors.categoriesModal);
    }

    function closeCategoryModal() {
        if (!selectors.categoriesModal) {
            return;
        }
        resetCategoryForm();
        closeModal(selectors.categoriesModal);
    }

    function filterMediaItems(term = '') {
        const normalized = term.trim().toLowerCase();
        return state.media.items.filter((item) => {
            if (!item || (item.type ?? '') !== 'images') {
                return false;
            }
            if (!normalized) {
                return true;
            }
            const name = String(item.name || '').toLowerCase();
            const file = String(item.file || '').toLowerCase();
            let tags = '';
            if (Array.isArray(item.tags)) {
                tags = item.tags.join(' ').toLowerCase();
            } else if (typeof item.tags === 'string') {
                tags = item.tags.toLowerCase();
            }
            return name.includes(normalized) || file.includes(normalized) || tags.includes(normalized);
        });
    }

    function renderMediaLibrary({ status = 'idle', items = [], search = '' } = {}) {
        const grid = selectors.mediaGrid;
        if (!grid) {
            return;
        }
        grid.setAttribute('aria-busy', status === 'loading' ? 'true' : 'false');
        if (status === 'loading') {
            grid.innerHTML = '<p class="events-media-status">Loading media…</p>';
            return;
        }
        if (status === 'error') {
            grid.innerHTML = '<p class="events-media-status events-media-status--error">Unable to load the media library. Please try again.</p>';
            return;
        }
        const list = Array.isArray(items) ? items.slice() : [];
        if (list.length === 0) {
            if (search) {
                grid.innerHTML = `<p class="events-media-status">No images match &ldquo;${escapeHtml(search)}&rdquo;. Try a different keyword.</p>`;
            } else {
                grid.innerHTML = '<p class="events-media-status">No images found in the media library. Upload images in the Media module.</p>';
            }
            return;
        }
        list.sort((a, b) => {
            const aName = String(a.name || a.file || '').toLowerCase();
            const bName = String(b.name || b.file || '').toLowerCase();
            return aName.localeCompare(bName, undefined, { sensitivity: 'base' });
        });
        grid.innerHTML = list
            .map((item) => {
                const file = escapeAttribute(item.file || '');
                const name = escapeHtml(item.name || item.file || 'Media item');
                const thumbSource = escapeAttribute(item.thumbnail || item.file || '');
                return `
                    <button type="button" class="events-media-item" data-events-media-item data-file="${file}" role="option">
                        <span class="events-media-thumb"><img src="${thumbSource}" alt="${name}"></span>
                        <span class="events-media-name">${name}</span>
                    </button>
                `;
            })
            .join('');
    }

    function loadMediaLibrary() {
        if (state.media.loading) {
            return;
        }
        state.media.loading = true;
        renderMediaLibrary({ status: 'loading' });
        fetch('modules/media/list_media.php?sort=name&order=asc')
            .then((response) => {
                if (!response.ok) {
                    throw new Error('Unable to load media');
                }
                return response.json();
            })
            .then((data) => {
                state.media.items = Array.isArray(data?.media) ? data.media : [];
                state.media.loaded = true;
                state.media.loading = false;
                const term = selectors.mediaSearch?.value || '';
                renderMediaLibrary({ items: filterMediaItems(term), search: term });
            })
            .catch(() => {
                state.media.loading = false;
                renderMediaLibrary({ status: 'error' });
            });
    }

    function openMediaPicker() {
        if (!selectors.mediaModal) {
            return;
        }
        openModal(selectors.mediaModal);
        if (selectors.mediaSearch) {
            selectors.mediaSearch.value = '';
        }
        if (state.media.loaded) {
            renderMediaLibrary({ items: filterMediaItems(''), search: '' });
        } else {
            renderMediaLibrary({ status: 'loading' });
            loadMediaLibrary();
        }
        setTimeout(() => {
            selectors.mediaSearch?.focus();
        }, 120);
    }

    function initMediaPicker() {
        if (!selectors.mediaModal) {
            return;
        }
        if (selectors.mediaSearch) {
            selectors.mediaSearch.addEventListener('input', () => {
                if (!state.media.loaded) {
                    return;
                }
                const term = selectors.mediaSearch.value || '';
                renderMediaLibrary({ items: filterMediaItems(term), search: term });
            });
        }
        selectors.mediaModal.addEventListener('click', (event) => {
            const item = event.target.closest('[data-events-media-item]');
            if (!item) {
                return;
            }
            event.preventDefault();
            const file = item.dataset.file || '';
            if (file && typeof state.media.currentSetter === 'function') {
                state.media.currentSetter(file);
            }
            state.media.currentSetter = null;
            closeModal(selectors.mediaModal);
        });
    }

    function initImagePicker(form) {
        if (!form) {
            return null;
        }
        const picker = form.querySelector('[data-events-image-picker]');
        if (!picker) {
            return null;
        }
        const input = picker.querySelector('input[name="image"]');
        const preview = picker.querySelector('[data-events-image-preview]');
        const chooseBtn = picker.querySelector('[data-events-image-open]');
        const clearBtn = picker.querySelector('[data-events-image-clear]');
        if (!input || !preview || !chooseBtn || !clearBtn) {
            return null;
        }

        function setValue(value) {
            const normalized = typeof value === 'string' ? value.trim() : '';
            input.value = normalized;
            if (normalized) {
                preview.innerHTML = `<img src="${escapeAttribute(normalized)}" alt="Event featured image preview">`;
                preview.classList.add('has-image');
                clearBtn.hidden = false;
            } else {
                preview.innerHTML = '<span class="events-image-placeholder">No image selected yet.</span>';
                preview.classList.remove('has-image');
                clearBtn.hidden = true;
            }
        }

        chooseBtn.addEventListener('click', (event) => {
            event.preventDefault();
            state.media.currentSetter = setValue;
            openMediaPicker();
        });

        clearBtn.addEventListener('click', (event) => {
            event.preventDefault();
            setValue('');
        });

        input.addEventListener('change', () => {
            setValue(input.value);
        });

        form.addEventListener('reset', () => {
            setTimeout(() => setValue(''), 0);
        });

        setValue(input.value);

        return { setValue };
    }

    function showToast(message, type = 'success') {
        if (!selectors.toast) {
            return;
        }
        selectors.toast.dataset.type = type;
        selectors.toast.querySelector('[data-events-toast-message]').textContent = message;
        selectors.toast.hidden = false;
        selectors.toast.classList.add('is-visible');
        setTimeout(() => {
            selectors.toast.classList.remove('is-visible');
            selectors.toast.hidden = true;
        }, 2400);
    }

    function buildQuery(params) {
        const query = new URLSearchParams();
        Object.entries(params || {}).forEach(([key, value]) => {
            if (value !== undefined && value !== null && String(value) !== '') {
                query.append(key, value);
            }
        });
        return query.toString();
    }

    function fetchJSON(action, options = {}) {
        const method = options.method || 'GET';
        const headers = options.headers || {};
        let url = `${endpoint}?action=${encodeURIComponent(action)}`;
        const fetchOptions = { method, headers: { ...headers } };
        if (method === 'GET' && options.params) {
            const query = buildQuery(options.params);
            if (query) {
                url += `&${query}`;
            }
        }
        if (method !== 'GET' && options.body) {
            fetchOptions.body = JSON.stringify(options.body);
            fetchOptions.headers['Content-Type'] = 'application/json';
        }
        return fetch(url, fetchOptions).then((response) => {
            if (!response.ok) {
                throw new Error('Request failed');
            }
            return response.json();
        });
    }

    function renderStats(stats) {
        if (selectors.stats.events) {
            selectors.stats.events.textContent = stats.total_events ?? state.eventRows.length;
        }
        if (selectors.stats.tickets) {
            selectors.stats.tickets.textContent = stats.total_tickets_sold ?? 0;
        }
        if (selectors.stats.revenue) {
            const revenue = stats.total_revenue ?? 0;
            selectors.stats.revenue.textContent = formatCurrency(revenue);
        }
    }

    function renderUpcoming(list) {
        const normalized = Array.isArray(list)
            ? list.map((item) => normalizeUpcomingItem(item)).filter((item) => item !== null)
            : [];

        state.upcoming = normalized;
        const todayMonth = startOfMonth(new Date());
        const earliestMonth = getEarliestEventMonth(normalized);
        const latestMonth = getLatestEventMonth(normalized);

        if (!state.calendar.currentMonth) {
            state.calendar.currentMonth = earliestMonth ? startOfMonth(earliestMonth) : todayMonth;
        } else {
            state.calendar.currentMonth = startOfMonth(state.calendar.currentMonth);
            if (state.calendar.currentMonth.getTime() < todayMonth.getTime()) {
                state.calendar.currentMonth = todayMonth;
            }
            if (latestMonth && state.calendar.currentMonth.getTime() > latestMonth.getTime()) {
                state.calendar.currentMonth = startOfMonth(latestMonth);
            }
        }

        if (normalized.length > 0) {
            const monthHasEvent = normalized.some((item) => {
                const date = parseDateValue(item.start);
                return date && isSameMonth(date, state.calendar.currentMonth);
            });
            if (!monthHasEvent && earliestMonth) {
                state.calendar.currentMonth = startOfMonth(earliestMonth);
            }
        } else {
            state.calendar.currentMonth = todayMonth;
        }

        renderUpcomingList(normalized);
        renderUpcomingCalendar();
    }

    function renderUpcomingList(list) {
        const listElement = selectors.upcoming?.list;
        if (!listElement) {
            return;
        }

        listElement.innerHTML = '';

        if (!Array.isArray(list) || list.length === 0) {
            const li = document.createElement('li');
            li.className = 'events-empty';
            li.textContent = 'No upcoming events scheduled. Create one to get started.';
            listElement.appendChild(li);
            return;
        }

        list.forEach((item) => {
            const li = document.createElement('li');
            li.className = 'events-upcoming-item';
            li.dataset.eventId = item.id || '';

            const primary = document.createElement('div');
            primary.className = 'events-upcoming-primary';

            const title = document.createElement('span');
            title.className = 'events-upcoming-title';
            title.textContent = item.title || 'Untitled event';
            primary.appendChild(title);

            const date = document.createElement('span');
            date.className = 'events-upcoming-date';
            const details = [formatDate(item.start)];
            if (item.occurrence && item.occurrence.index > 0) {
                details.push(`Occurrence #${item.occurrence.index + 1}`);
            }
            if (item.recurrence_summary) {
                details.push(item.recurrence_summary);
            }
            date.textContent = details.filter((part) => part && part !== 'Date TBD').join(' · ') || 'Date TBD';
            primary.appendChild(date);

            const meta = document.createElement('div');
            meta.className = 'events-upcoming-meta';

            const tickets = document.createElement('span');
            tickets.className = 'events-upcoming-stat';
            tickets.dataset.label = 'Tickets sold';
            tickets.textContent = String(item.tickets_sold ?? 0);
            meta.appendChild(tickets);

            const revenue = document.createElement('span');
            revenue.className = 'events-upcoming-stat';
            revenue.dataset.label = 'Revenue';
            revenue.textContent = formatCurrency(item.revenue ?? 0);
            meta.appendChild(revenue);

            li.appendChild(primary);
            li.appendChild(meta);
            listElement.appendChild(li);
        });
    }

    function renderUpcomingCalendar() {
        const calendarSelectors = selectors.upcoming?.calendar;
        if (!calendarSelectors || !calendarSelectors.grid) {
            return;
        }

        const todayMonth = startOfMonth(new Date());
        const monthDate = state.calendar.currentMonth ? startOfMonth(state.calendar.currentMonth) : todayMonth;
        state.calendar.currentMonth = monthDate;

        if (calendarSelectors.label) {
            calendarSelectors.label.textContent = formatMonthLabel(monthDate);
        }

        if (calendarSelectors.empty) {
            calendarSelectors.empty.hidden = state.upcoming.length > 0;
        }

        const eventsByDay = new Map();
        state.upcoming.forEach((event) => {
            const date = parseDateValue(event.start);
            if (!date) {
                return;
            }
            const key = formatDateKey(date);
            if (eventsByDay.has(key)) {
                eventsByDay.get(key).push(event);
            } else {
                eventsByDay.set(key, [event]);
            }
        });

        const firstOfMonth = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
        const startDay = firstOfMonth.getDay();
        const daysInMonth = new Date(monthDate.getFullYear(), monthDate.getMonth() + 1, 0).getDate();
        const totalCells = Math.ceil((startDay + daysInMonth) / 7) * 7;

        calendarSelectors.grid.innerHTML = '';

        for (let index = 0; index < totalCells; index += 1) {
            const dayNumber = index - startDay + 1;
            const cell = document.createElement('div');
            cell.className = 'events-calendar-day';

            if (dayNumber < 1 || dayNumber > daysInMonth) {
                cell.classList.add('is-muted');
                calendarSelectors.grid.appendChild(cell);
                continue;
            }

            const cellDate = new Date(monthDate.getFullYear(), monthDate.getMonth(), dayNumber);
            const key = formatDateKey(cellDate);
            cell.dataset.date = key;

            const dateLabel = document.createElement('div');
            dateLabel.className = 'events-calendar-date';
            dateLabel.textContent = String(dayNumber);
            cell.appendChild(dateLabel);

            const dayEvents = eventsByDay.has(key)
                ? eventsByDay.get(key).slice().sort(compareUpcomingByStart)
                : [];

            if (dayEvents.length > 0) {
                cell.classList.add('has-events');
                const listElement = document.createElement('ul');
                listElement.className = 'events-calendar-events';

                dayEvents.forEach((event) => {
                    const item = document.createElement('li');
                    item.className = 'events-calendar-event';
                    item.dataset.eventId = event.id || '';

                    const timeLabel = formatTimeLabel(event.start);
                    if (timeLabel) {
                        const timeSpan = document.createElement('span');
                        timeSpan.className = 'events-calendar-event-time';
                        timeSpan.textContent = timeLabel;
                        item.appendChild(timeSpan);
                    }

                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'events-calendar-event-title';
                    titleSpan.textContent = event.title || 'Untitled event';
                    item.appendChild(titleSpan);

                    listElement.appendChild(item);
                });

                cell.appendChild(listElement);
            }

            calendarSelectors.grid.appendChild(cell);
        }

        if (calendarSelectors.prev) {
            const disablePrev = monthDate.getTime() <= todayMonth.getTime();
            calendarSelectors.prev.disabled = disablePrev;
        }

        if (calendarSelectors.next) {
            const latestMonth = getLatestEventMonth();
            if (latestMonth) {
                calendarSelectors.next.disabled = monthDate.getTime() >= latestMonth.getTime();
            } else {
                calendarSelectors.next.disabled = true;
            }
        }
    }

    function createStatusBadge(status) {
        const span = document.createElement('span');
        const value = String(status || 'draft');
        span.className = `events-status events-status--${value}`;
        span.textContent = value.charAt(0).toUpperCase() + value.slice(1);
        return span;
    }

    function createEventRow(row) {
        const tr = document.createElement('tr');
        tr.dataset.eventId = row.id;
        const startValue = row.next_start || row.start;
        const endValue = row.next_end || row.end;
        const startLabel = formatDate(startValue);
        const endLabel = endValue ? formatDate(endValue) : '';
        const recurrenceSummary = row.recurrence_summary
            ? `<div class="events-table-sub">${escapeHtml(row.recurrence_summary)}</div>`
            : '';
        tr.innerHTML = `
            <td>
                <div class="events-table-title">${row.title}</div>
                <div class="events-table-sub">${row.location || ''}</div>
            </td>
            <td>
                <div>${escapeHtml(startLabel)}</div>
                ${endLabel ? `<div class="events-table-sub">Ends ${escapeHtml(endLabel)}</div>` : ''}
                ${recurrenceSummary}
            </td>
            <td>${row.location || 'TBA'}</td>
            <td>${row.tickets_sold ?? 0} / ${row.capacity ?? 0}</td>
            <td>${formatCurrency(row.revenue ?? 0)}</td>
            <td data-status></td>
            <td class="events-table-actions">
                <button type="button" class="events-action" data-events-action="edit" data-id="${row.id}">
                    <i class="fa-solid fa-pen"></i><span class="sr-only">Edit</span>
                </button>
                <button type="button" class="events-action" data-events-action="sales" data-id="${row.id}">
                    <i class="fa-solid fa-chart-column"></i><span class="sr-only">View sales</span>
                </button>
                <button type="button" class="events-action" data-events-action="end" data-id="${row.id}">
                    <i class="fa-solid fa-flag-checkered"></i><span class="sr-only">End event</span>
                </button>
                <button type="button" class="events-action danger" data-events-action="delete" data-id="${row.id}">
                    <i class="fa-solid fa-trash"></i><span class="sr-only">Delete</span>
                </button>
            </td>
        `;
        const badgeCell = tr.querySelector('[data-status]');
        badgeCell.appendChild(createStatusBadge(row.status));
        return tr;
    }

    function applyEventFilters(rows) {
        return rows.filter((row) => {
            const matchesStatus = !state.filters.status || row.status === state.filters.status;
            const term = state.filters.search.trim().toLowerCase();
            const matchesSearch = !term || `${row.title} ${row.location}`.toLowerCase().includes(term);
            return matchesStatus && matchesSearch;
        });
    }

    function renderEventsTable() {
        if (!selectors.tableBody) {
            return;
        }
        selectors.tableBody.innerHTML = '';
        const filtered = applyEventFilters(state.eventRows);
        if (filtered.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 4;
            cell.className = 'events-empty';
            cell.textContent = 'No events match the current filters.';
            row.appendChild(cell);
            selectors.tableBody.appendChild(row);
            return;
        }
        filtered.forEach((row) => {
            selectors.tableBody.appendChild(createEventRow(row));
        });
    }

    function populateEventSelect(select) {
        if (!select) {
            return;
        }
        const current = select.value;
        select.innerHTML = '';
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = 'All events';
        select.appendChild(defaultOption);
        Array.from(state.events.values())
            .sort((a, b) => {
                const aStart = a.next_start || a.start;
                const bStart = b.next_start || b.start;
                const aTime = aStart ? new Date(aStart).getTime() : 0;
                const bTime = bStart ? new Date(bStart).getTime() : 0;
                return aTime - bTime;
            })
            .forEach((event) => {
                const option = document.createElement('option');
                option.value = event.id;
                option.textContent = event.title || 'Untitled event';
                select.appendChild(option);
            });
        if (current && select.querySelector(`option[value="${current}"]`)) {
            select.value = current;
        }
    }

    function renderOrdersTable() {
        if (!selectors.orders.body) {
            return;
        }
        selectors.orders.body.innerHTML = '';
        if (!Array.isArray(state.orders) || state.orders.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 8;
            cell.className = 'events-empty';
            cell.textContent = 'No orders found for the selected filters.';
            row.appendChild(cell);
            selectors.orders.body.appendChild(row);
            return;
        }
        state.orders.forEach((order) => {
            const tr = document.createElement('tr');
            const totalTickets = typeof order.tickets === 'number'
                ? order.tickets
                : Array.isArray(order.line_items)
                    ? order.line_items.reduce((sum, item) => sum + (item.quantity || 0), 0)
                    : 0;
            tr.innerHTML = `
                <td>${escapeHtml(order.id || '')}</td>
                <td>
                    <div class="events-table-title">${escapeHtml(order.event || 'Event')}</div>
                    ${order.event_id ? `<div class="events-table-sub">#${escapeHtml(order.event_id)}</div>` : ''}
                </td>
                <td>${escapeHtml(order.buyer_name || '')}</td>
                <td>${totalTickets}</td>
                <td>${formatCurrency(order.amount ?? 0)}</td>
                <td data-status></td>
                <td>${formatDate(order.ordered_at)}</td>
                <td class="is-actions">
                    <button type="button" class="events-order-manage" data-events-order-manage data-id="${escapeAttribute(order.id)}">
                        Manage
                    </button>
                </td>
            `;
            const statusCell = tr.querySelector('[data-status]');
            if (statusCell) {
                statusCell.appendChild(createStatusBadge(order.status));
            }
            selectors.orders.body.appendChild(tr);
        });
    }

    function renderReportsTable() {
        const table = selectors.reports.tableBody;
        if (!table) {
            return;
        }
        table.innerHTML = '';
        if (!Array.isArray(state.salesSummary) || state.salesSummary.length === 0) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 5;
            cell.className = 'events-empty';
            cell.textContent = 'No report data available yet.';
            row.appendChild(cell);
            table.appendChild(row);
            return;
        }
        state.salesSummary.forEach((report) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="events-table-title">${escapeHtml(report.title || 'Untitled event')}</div>
                </td>
                <td>${report.tickets_sold ?? 0}</td>
                <td>${formatCurrency(report.revenue ?? 0)}</td>
                <td>${formatCurrency(report.refunded ?? 0)}</td>
                <td data-status></td>
            `;
            const statusCell = row.querySelector('[data-status]');
            if (statusCell) {
                statusCell.appendChild(createStatusBadge(report.status));
            }
            table.appendChild(row);
        });
    }

    function collectOrderLines() {
        const container = selectors.orderEditor.lines;
        if (!container) {
            return [];
        }
        const rows = Array.from(container.querySelectorAll('[data-order-line]'));
        return rows.map((row) => {
            updateLineTotal(row);
            const priceInput = row.querySelector('[data-order-line-price]');
            const quantityInput = row.querySelector('[data-order-line-quantity]');
            let price = Number.parseFloat(priceInput?.value ?? '0');
            if (!Number.isFinite(price) || price < 0) {
                price = 0;
            }
            let quantity = Number.parseInt(quantityInput?.value ?? '0', 10);
            if (!Number.isFinite(quantity) || quantity < 0) {
                quantity = 0;
            }
            return {
                ticket_id: row.dataset.ticketId || '',
                name: row.dataset.ticketName || 'Ticket',
                price,
                quantity,
                subtotal: price * quantity,
            };
        });
    }

    function updateLineTotal(row) {
        if (!row) {
            return;
        }
        const priceInput = row.querySelector('[data-order-line-price]');
        const quantityInput = row.querySelector('[data-order-line-quantity]');
        let price = Number.parseFloat(priceInput?.value ?? '0');
        if (!Number.isFinite(price) || price < 0) {
            price = 0;
        }
        let quantity = Number.parseInt(quantityInput?.value ?? '0', 10);
        if (!Number.isFinite(quantity) || quantity < 0) {
            quantity = 0;
        }
        if (priceInput) {
            priceInput.value = price.toFixed(2);
        }
        if (quantityInput) {
            quantityInput.value = String(quantity);
        }
        const total = price * quantity;
        const totalEl = row.querySelector('[data-order-line-total]');
        if (totalEl) {
            totalEl.textContent = formatCurrency(total);
        }
    }

    function createOrderLine(line) {
        const row = document.createElement('div');
        row.className = 'events-order-line';
        row.dataset.orderLine = 'true';
        row.dataset.ticketId = line.ticket_id || '';
        row.dataset.ticketName = line.name || 'Ticket';
        row.innerHTML = `
            <div class="events-order-line-header">
                <div>
                    <div class="events-order-line-name">${escapeHtml(line.name || 'Ticket')}</div>
                    <div class="events-order-line-meta">ID ${escapeHtml(line.ticket_id || '')}</div>
                </div>
                <button type="button" class="events-order-line-remove" data-order-line-remove>&times;<span class="sr-only">Remove ticket</span></button>
            </div>
            <div class="events-order-line-grid">
                <label class="events-order-line-field">
                    <span>Price</span>
                    <input type="number" min="0" step="0.01" value="${Number(line.price || 0).toFixed(2)}" data-order-line-price>
                </label>
                <label class="events-order-line-field">
                    <span>Quantity</span>
                    <input type="number" min="0" step="1" value="${Math.max(0, Number.parseInt(line.quantity ?? 0, 10) || 0)}" data-order-line-quantity>
                </label>
                <div class="events-order-line-total" data-order-line-total>${formatCurrency((line.price || 0) * (line.quantity || 0))}</div>
            </div>
        `;
        return row;
    }

    function renderOrderLines(lines) {
        const container = selectors.orderEditor.lines;
        if (!container) {
            return;
        }
        container.innerHTML = '';
        if (!Array.isArray(lines) || lines.length === 0) {
            container.innerHTML = '<p class="events-order-empty">No tickets on this order yet.</p>';
            return;
        }
        lines.forEach((line) => {
            const row = createOrderLine(line);
            container.appendChild(row);
            updateLineTotal(row);
        });
    }

    function updateOrderAddOptions(detail = state.orderEditor.detail) {
        const select = selectors.orderEditor.addSelect;
        if (!select) {
            return;
        }
        select.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Choose ticket type';
        select.appendChild(placeholder);
        const available = detail?.available_tickets;
        if (!Array.isArray(available) || available.length === 0) {
            select.disabled = true;
            if (selectors.orderEditor.addButton) {
                selectors.orderEditor.addButton.disabled = true;
            }
            return;
        }
        const used = new Set();
        if (selectors.orderEditor.lines) {
            selectors.orderEditor.lines.querySelectorAll('[data-order-line]').forEach((row) => {
                used.add(row.dataset.ticketId || '');
            });
        }
        available.forEach((ticket) => {
            const ticketId = String(ticket.ticket_id || '').trim();
            if (ticketId === '' || used.has(ticketId)) {
                return;
            }
            const option = document.createElement('option');
            option.value = ticketId;
            option.textContent = `${ticket.name || 'Ticket'} — ${formatCurrency(ticket.price || 0)}`;
            select.appendChild(option);
        });
        select.disabled = select.options.length <= 1;
        if (selectors.orderEditor.addButton) {
            selectors.orderEditor.addButton.disabled = select.disabled;
        }
        if (!select.disabled) {
            select.value = '';
        }
    }

    function updateOrderSummary() {
        const totals = selectors.orderEditor.totals;
        const statusValue = selectors.orderEditor.status?.value || 'paid';
        const lines = collectOrderLines();
        const subtotal = lines.reduce((sum, line) => sum + (line.price * line.quantity), 0);
        const refunds = statusValue === 'refunded' ? subtotal : 0;
        const net = subtotal - refunds;
        if (totals.subtotal) {
            totals.subtotal.textContent = formatCurrency(subtotal);
        }
        if (totals.refunds) {
            totals.refunds.textContent = formatCurrency(refunds);
        }
        if (totals.net) {
            totals.net.textContent = formatCurrency(net);
        }
        const breakdown = selectors.orderEditor.breakdown;
        if (breakdown) {
            breakdown.innerHTML = '';
            if (lines.length === 0) {
                const empty = document.createElement('p');
                empty.className = 'events-order-empty';
                empty.textContent = 'Ticket breakdown will appear here.';
                breakdown.appendChild(empty);
            } else {
                const list = document.createElement('ul');
                list.className = 'events-order-breakdown-list';
                lines.forEach((line) => {
                    const item = document.createElement('li');
                    item.className = 'events-order-breakdown-item';
                    item.innerHTML = `
                        <span class="events-order-breakdown-name">${escapeHtml(line.name || 'Ticket')}</span>
                        <span class="events-order-breakdown-meta">${line.quantity} × ${formatCurrency(line.price)} = ${formatCurrency(line.price * line.quantity)}</span>
                    `;
                    list.appendChild(item);
                });
                breakdown.appendChild(list);
            }
        }
        return { subtotal, refunds, net, lines };
    }

    function fillOrderEditor(detail) {
        const form = selectors.orderEditor.form;
        if (!form || !detail) {
            return;
        }
        state.orderEditor.detail = {
            ...detail,
            available_tickets: Array.isArray(detail.available_tickets) ? detail.available_tickets : [],
        };
        const idInput = form.querySelector('[name="id"]');
        if (idInput) {
            idInput.value = detail.id || '';
        }
        const eventIdInput = form.querySelector('[name="event_id"]');
        if (eventIdInput) {
            eventIdInput.value = detail.event_id || '';
        }
        const buyerInput = form.querySelector('[name="buyer_name"]');
        if (buyerInput) {
            buyerInput.value = detail.buyer_name || '';
        }
        const orderedAtInput = form.querySelector('[name="ordered_at"]');
        if (orderedAtInput) {
            orderedAtInput.value = toLocalDateTimeInput(detail.ordered_at);
        }
        if (selectors.orderEditor.status) {
            selectors.orderEditor.status.value = detail.status || 'paid';
        }
        if (selectors.orderEditor.title) {
            selectors.orderEditor.title.textContent = detail.id ? `Order ${detail.id}` : 'Order';
        }
        if (selectors.orderEditor.event) {
            selectors.orderEditor.event.textContent = detail.event?.title || 'Event';
        }
        renderOrderLines(Array.isArray(detail.line_items) ? detail.line_items : []);
        updateOrderAddOptions(state.orderEditor.detail);
        updateOrderSummary();
    }

    function resetOrderEditor() {
        state.orderEditor.detail = null;
        if (selectors.orderEditor.form) {
            selectors.orderEditor.form.reset();
            const idInput = selectors.orderEditor.form.querySelector('[name="id"]');
            if (idInput) {
                idInput.value = '';
            }
            const eventIdInput = selectors.orderEditor.form.querySelector('[name="event_id"]');
            if (eventIdInput) {
                eventIdInput.value = '';
            }
        }
        if (selectors.orderEditor.lines) {
            selectors.orderEditor.lines.innerHTML = '<p class="events-order-empty">No tickets on this order yet.</p>';
        }
        if (selectors.orderEditor.breakdown) {
            selectors.orderEditor.breakdown.innerHTML = '<p class="events-order-empty">Ticket breakdown will appear here.</p>';
        }
        if (selectors.orderEditor.totals) {
            if (selectors.orderEditor.totals.subtotal) {
                selectors.orderEditor.totals.subtotal.textContent = formatCurrency(0);
            }
            if (selectors.orderEditor.totals.refunds) {
                selectors.orderEditor.totals.refunds.textContent = formatCurrency(0);
            }
            if (selectors.orderEditor.totals.net) {
                selectors.orderEditor.totals.net.textContent = formatCurrency(0);
            }
        }
        if (selectors.orderEditor.addSelect) {
            selectors.orderEditor.addSelect.innerHTML = '';
            selectors.orderEditor.addSelect.disabled = true;
        }
        if (selectors.orderEditor.addButton) {
            selectors.orderEditor.addButton.disabled = true;
        }
        if (selectors.orderEditor.title) {
            selectors.orderEditor.title.textContent = 'Order';
        }
        if (selectors.orderEditor.event) {
            selectors.orderEditor.event.textContent = '';
        }
    }

    function addOrderLine() {
        const detail = state.orderEditor.detail;
        if (!detail) {
            return;
        }
        const select = selectors.orderEditor.addSelect;
        if (!select) {
            return;
        }
        let ticketId = select.value;
        if (!ticketId && select.options.length > 1) {
            ticketId = select.options[1].value;
            select.value = ticketId;
        }
        if (!ticketId) {
            showToast('No additional ticket types available.', 'error');
            return;
        }
        const ticket = detail.available_tickets.find((item) => String(item.ticket_id) === ticketId);
        if (!ticket) {
            showToast('Ticket type not found.', 'error');
            return;
        }
        const container = selectors.orderEditor.lines;
        if (!container) {
            return;
        }
        const existing = container.querySelector(`[data-order-line][data-ticket-id="${ticketId}"]`);
        if (existing) {
            const quantityInput = existing.querySelector('[data-order-line-quantity]');
            if (quantityInput) {
                quantityInput.value = String(Number.parseInt(quantityInput.value || '0', 10) + 1);
                updateLineTotal(existing);
                updateOrderSummary();
                updateOrderAddOptions(detail);
                select.value = '';
            }
            return;
        }
        if (container.querySelector('.events-order-empty')) {
            container.innerHTML = '';
        }
        const row = createOrderLine({
            ticket_id: ticket.ticket_id,
            name: ticket.name,
            price: ticket.price,
            quantity: 1,
        });
        container.appendChild(row);
        updateLineTotal(row);
        updateOrderSummary();
        updateOrderAddOptions(detail);
        select.value = '';
    }

    function openOrderModal(orderId) {
        const modal = selectors.orderEditor.modal;
        if (!modal || !orderId) {
            return;
        }
        fetchJSON('get_order', { params: { id: orderId } })
            .then((response) => {
                if (!response || !response.order) {
                    throw new Error('Order not found');
                }
                fillOrderEditor(response.order);
                openModal(modal);
            })
            .catch(() => {
                showToast('Unable to load order.', 'error');
            });
    }

    function serializeOrderForm() {
        const form = selectors.orderEditor.form;
        if (!form) {
            return null;
        }
        updateOrderSummary();
        const formData = new FormData(form);
        const id = String(formData.get('id') || '').trim();
        if (id === '') {
            showToast('Missing order information.', 'error');
            return null;
        }
        const buyerName = String(formData.get('buyer_name') || '').trim();
        if (buyerName === '') {
            showToast('Buyer name is required.', 'error');
            return null;
        }
        const status = String(formData.get('status') || 'paid').toLowerCase();
        const orderedAtRaw = String(formData.get('ordered_at') || '').trim();
        const orderedAt = orderedAtRaw ? fromLocalDateTimeInput(orderedAtRaw) : '';
        const lines = collectOrderLines().filter((line) => line.ticket_id && line.quantity > 0);
        const tickets = lines.map((line) => {
            const priceValue = Number.isFinite(line.price) ? line.price : 0;
            return {
                ticket_id: line.ticket_id,
                quantity: line.quantity,
                price: Number(priceValue.toFixed(2)),
            };
        });

        return {
            id,
            event_id: String(formData.get('event_id') || '').trim(),
            buyer_name: buyerName,
            status,
            ordered_at,
            tickets,
        };
    }

    function computeRevenueMetrics() {
        const totalRevenue = state.salesSummary.reduce((sum, report) => sum + (Number(report.revenue) || 0), 0);
        const refunds = state.salesSummary.reduce((sum, report) => sum + (Number(report.refunded) || 0), 0);
        const totalOrders = state.orders.length;
        let paidTotal = 0;
        let paidCount = 0;
        let refundOrders = 0;
        state.orders.forEach((order) => {
            const amount = Number(order.amount || 0);
            if (order.status === 'refunded') {
                refundOrders += 1;
            } else if (order.status === 'paid') {
                paidTotal += amount;
                paidCount += 1;
            }
        });
        const averageOrder = paidCount > 0 ? paidTotal / paidCount : 0;
        return {
            totalRevenue,
            netRevenue: totalRevenue - refunds,
            totalOrders,
            averageOrder,
            paidOrdersCount: paidCount,
            refundsTotal: refunds,
            refundOrders,
        };
    }

    function renderReportMetrics(metrics) {
        const metricEls = selectors.reports.metrics;
        if (!metricEls) {
            return;
        }
        const revenueEl = metricEls.revenue;
        if (revenueEl) {
            const value = revenueEl.querySelector('[data-value]');
            const meta = revenueEl.querySelector('[data-meta]');
            if (value) {
                value.textContent = formatCurrency(metrics.totalRevenue);
            }
            if (meta) {
                const netText = formatCurrency(metrics.netRevenue);
                const ordersText = metrics.totalOrders === 1 ? '1 order' : `${metrics.totalOrders} orders`;
                meta.textContent = `${ordersText} · Net ${netText}`;
            }
        }
        const averageEl = metricEls.averageOrder;
        if (averageEl) {
            const value = averageEl.querySelector('[data-value]');
            const meta = averageEl.querySelector('[data-meta]');
            if (value) {
                value.textContent = formatCurrency(metrics.averageOrder);
            }
            if (meta) {
                meta.textContent = metrics.paidOrdersCount > 0
                    ? (metrics.paidOrdersCount === 1 ? '1 paid order' : `${metrics.paidOrdersCount} paid orders`)
                    : 'No paid orders yet.';
            }
        }
        const refundsEl = metricEls.refunds;
        if (refundsEl) {
            const value = refundsEl.querySelector('[data-value]');
            const meta = refundsEl.querySelector('[data-meta]');
            if (value) {
                value.textContent = formatCurrency(metrics.refundsTotal);
            }
            if (meta) {
                meta.textContent = metrics.refundOrders
                    ? `${metrics.refundOrders} refunded ${metrics.refundOrders === 1 ? 'order' : 'orders'}.`
                    : 'No refunds issued.';
            }
        }
    }

    function computeInsights() {
        const topEvent = [...state.salesSummary]
            .filter((item) => (Number(item.revenue) || 0) > 0)
            .sort((a, b) => (Number(b.revenue) || 0) - (Number(a.revenue) || 0))[0] || null;
        const ticketTotals = new Map();
        state.orders.forEach((order) => {
            (order.line_items || []).forEach((line) => {
                if (!line || !line.ticket_id) {
                    return;
                }
                const key = String(line.ticket_id);
                const quantity = Number(line.quantity || 0);
                if (!ticketTotals.has(key)) {
                    ticketTotals.set(key, {
                        id: key,
                        name: line.name || key,
                        quantity: 0,
                    });
                }
                ticketTotals.get(key).quantity += quantity;
            });
        });
        const topTicket = [...ticketTotals.values()]
            .filter((item) => item.quantity > 0)
            .sort((a, b) => b.quantity - a.quantity)[0] || null;
        const buyerTotals = new Map();
        state.orders.forEach((order) => {
            if (order.status === 'refunded') {
                return;
            }
            const buyer = order.buyer_name || 'Unknown buyer';
            const amount = Number(order.amount || 0);
            buyerTotals.set(buyer, (buyerTotals.get(buyer) || 0) + amount);
        });
        const topBuyerEntry = [...buyerTotals.entries()]
            .filter(([, amount]) => amount > 0)
            .sort((a, b) => b[1] - a[1])[0];
        const topBuyer = topBuyerEntry ? { name: topBuyerEntry[0], amount: topBuyerEntry[1] } : null;
        return { topEvent, topTicket, topBuyer };
    }

    function renderInsights(insights) {
        const container = selectors.reports.insights;
        if (!container) {
            return;
        }
        container.querySelectorAll('[data-insight]').forEach((card) => {
            const type = card.dataset.insight;
            const valueEl = card.querySelector('[data-insight-value]');
            const metaEl = card.querySelector('[data-insight-meta]');
            let valueText = '—';
            let metaText = 'No data available yet.';
            switch (type) {
                case 'top-event': {
                    const topEvent = insights.topEvent;
                    if (topEvent && (Number(topEvent.revenue) || 0) > 0) {
                        valueText = topEvent.title || 'Event';
                        metaText = `${formatCurrency(Number(topEvent.revenue) || 0)} total revenue`;
                    } else {
                        metaText = 'No revenue recorded yet.';
                    }
                    break;
                }
                case 'top-ticket': {
                    const topTicket = insights.topTicket;
                    if (topTicket && topTicket.quantity > 0) {
                        valueText = topTicket.name || 'Ticket';
                        metaText = `${topTicket.quantity} tickets sold`;
                    } else {
                        metaText = 'No ticket sales yet.';
                    }
                    break;
                }
                case 'top-buyer': {
                    const topBuyer = insights.topBuyer;
                    if (topBuyer && (Number(topBuyer.amount) || 0) > 0) {
                        valueText = topBuyer.name || 'Customer';
                        metaText = `${formatCurrency(Number(topBuyer.amount) || 0)} in purchases`;
                    } else {
                        metaText = 'No paid customers yet.';
                    }
                    break;
                }
                default:
                    break;
            }
            if (valueEl) {
                valueEl.textContent = valueText;
            }
            if (metaEl) {
                metaEl.textContent = metaText;
            }
        });
    }

    function updateInsightsAndMetrics() {
        renderReportMetrics(computeRevenueMetrics());
        renderInsights(computeInsights());
    }

    function handleOrderForm() {
        const form = selectors.orderEditor.form;
        if (!form) {
            return;
        }
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const payload = serializeOrderForm();
            if (!payload) {
                return;
            }
            fetchJSON('save_order', { method: 'POST', body: payload })
                .then(() => {
                    showToast('Order updated.');
                    closeModal(selectors.orderEditor.modal);
                    refreshAll();
                })
                .catch(() => {
                    showToast('Unable to save order.', 'error');
                });
        });
        if (selectors.orderEditor.lines) {
            selectors.orderEditor.lines.addEventListener('input', (event) => {
                const row = event.target.closest('[data-order-line]');
                if (!row) {
                    return;
                }
                if (event.target.matches('[data-order-line-price], [data-order-line-quantity]')) {
                    updateLineTotal(row);
                    updateOrderSummary();
                    updateOrderAddOptions();
                }
            });
            selectors.orderEditor.lines.addEventListener('click', (event) => {
                const removeBtn = event.target.closest('[data-order-line-remove]');
                if (!removeBtn) {
                    return;
                }
                event.preventDefault();
                const row = removeBtn.closest('[data-order-line]');
                if (row) {
                    row.remove();
                }
                const container = selectors.orderEditor.lines;
                if (container && !container.querySelector('[data-order-line]')) {
                    container.innerHTML = '<p class="events-order-empty">No tickets on this order yet.</p>';
                }
                updateOrderSummary();
                updateOrderAddOptions();
            });
        }
        if (selectors.orderEditor.status) {
            selectors.orderEditor.status.addEventListener('change', () => {
                updateOrderSummary();
            });
        }
        if (selectors.orderEditor.addButton) {
            selectors.orderEditor.addButton.addEventListener('click', () => {
                addOrderLine();
            });
        }
    }

    function openModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.add('is-open');
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        if (modal === selectors.mediaModal) {
            state.media.currentSetter = null;
        }
        if (modal === selectors.orderEditor.modal) {
            resetOrderEditor();
            return;
        }
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            const editor = form.querySelector('[data-events-editor]');
            if (editor) {
                editor.innerHTML = '';
            }
            const tickets = form.querySelector('[data-events-tickets]');
            if (tickets) {
                tickets.innerHTML = '<div class="events-ticket-empty">No ticket types yet. Add one to begin selling.</div>';
            }
            if (form.__recurrenceControls && typeof form.__recurrenceControls.sync === 'function') {
                form.__recurrenceControls.sync();
            }
        }
    }

    function bindModalDismissals() {
        document.querySelectorAll('[data-events-close]').forEach((button) => {
            button.addEventListener('click', () => {
                const backdrop = button.closest('.events-modal-backdrop');
                if (!backdrop) {
                    return;
                }
                if (backdrop === selectors.categoriesModal) {
                    closeCategoryModal();
                } else {
                    closeModal(backdrop);
                }
            });
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal(selectors.modal);
                closeModal(selectors.confirmModal);
                closeModal(selectors.mediaModal);
                closeModal(selectors.orderEditor.modal);
                closeCategoryModal();
            }
        });
    }

    function fillEventForm(eventData) {
        const modal = selectors.modal;
        if (!modal) {
            return;
        }
        const form = modal.querySelector('[data-events-form="event"]');
        form.reset();
        form.querySelector('[name="id"]').value = eventData?.id || '';
        form.querySelector('[name="title"]').value = eventData?.title || '';
        form.querySelector('[name="location"]').value = eventData?.location || '';
        if (!form.__imagePicker) {
            form.__imagePicker = initImagePicker(form);
        }
        if (form.__imagePicker && typeof form.__imagePicker.setValue === 'function') {
            form.__imagePicker.setValue(eventData?.image || '');
        } else {
            const imageInput = form.querySelector('[name="image"]');
            if (imageInput) {
                imageInput.value = eventData?.image || '';
            }
        }
        form.querySelector('[name="start"]').value = eventData?.start ? eventData.start.substring(0, 16) : '';
        form.querySelector('[name="end"]').value = eventData?.end ? eventData.end.substring(0, 16) : '';
        form.querySelector(`[name="status"][value="${eventData?.status || 'draft'}"]`).checked = true;
        const recurrenceControls = setupRecurrence(form);
        const recurrenceData = eventData?.recurrence || {};
        if (recurrenceControls) {
            if (recurrenceControls.frequency) {
                recurrenceControls.frequency.value = recurrenceData.frequency || 'none';
            }
            if (recurrenceControls.interval) {
                recurrenceControls.interval.value = recurrenceData.interval ?? 1;
            }
            if (recurrenceControls.unit) {
                recurrenceControls.unit.value = recurrenceData.unit || 'days';
            }
            const endTypeValue = recurrenceData.end_type || 'never';
            recurrenceControls.endType.forEach((radio) => {
                radio.checked = radio.value === endTypeValue;
            });
            if (recurrenceControls.endCount) {
                const countValue = Number.parseInt(recurrenceData.end_count ?? 0, 10);
                recurrenceControls.endCount.value = countValue > 0 ? String(countValue) : '';
            }
            if (recurrenceControls.endDate) {
                recurrenceControls.endDate.value = recurrenceData.end_date || '';
            }
            if (typeof recurrenceControls.sync === 'function') {
                recurrenceControls.sync();
            }
        }
        const editor = form.querySelector('[data-events-editor]');
        const target = form.querySelector('[data-events-editor-target]');
        if (editor && target) {
            editor.innerHTML = eventData?.description || '';
            target.value = eventData?.description || '';
        }
        renderCategoryOptions(Array.isArray(eventData?.categories) ? eventData.categories : []);
        const ticketContainer = form.querySelector('[data-events-tickets]');
        ticketContainer.innerHTML = '';
        const tickets = Array.isArray(eventData?.tickets) ? eventData.tickets : [];
        if (tickets.length === 0) {
            ticketContainer.innerHTML = '<div class="events-ticket-empty">No ticket types yet. Add one to begin selling.</div>';
        } else {
            tickets.forEach((ticket) => addTicketRow(ticketContainer, ticket));
        }
    }

    function addTicketRow(container, ticket = {}) {
        if (!container) {
            return;
        }
        const row = document.createElement('div');
        row.className = 'events-ticket-row';
        row.innerHTML = `
            <input type="hidden" data-ticket-field="id" value="${ticket.id || ''}">
            <label>
                <span>Name</span>
                <input type="text" data-ticket-field="name" value="${ticket.name || ''}" required>
            </label>
            <label>
                <span>Price</span>
                <input type="number" min="0" step="0.01" data-ticket-field="price" value="${ticket.price ?? 0}" required>
            </label>
            <label>
                <span>Quantity</span>
                <input type="number" min="0" step="1" data-ticket-field="quantity" value="${ticket.quantity ?? 0}" required>
            </label>
            <label class="events-ticket-toggle">
                <input type="checkbox" data-ticket-field="enabled" ${ticket.enabled === false ? '' : 'checked'}>
                <span>Enabled</span>
            </label>
            <button type="button" class="events-action danger" data-ticket-remove>
                <i class="fa-solid fa-times"></i><span class="sr-only">Remove ticket</span>
            </button>
        `;
        const empty = container.querySelector('.events-ticket-empty');
        if (empty) {
            empty.remove();
        }
        container.appendChild(row);
    }

    function gatherTickets(container) {
        const rows = Array.from(container.querySelectorAll('.events-ticket-row'));
        return rows.map((row) => ({
            id: row.querySelector('[data-ticket-field="id"]').value || undefined,
            name: row.querySelector('[data-ticket-field="name"]').value.trim(),
            price: parseFloat(row.querySelector('[data-ticket-field="price"]').value || '0'),
            quantity: parseInt(row.querySelector('[data-ticket-field="quantity"]').value || '0', 10),
            enabled: row.querySelector('[data-ticket-field="enabled"]').checked,
        })).filter((ticket) => ticket.name !== '');
    }

    function serializeForm(form) {
        const formData = new FormData(form);
        const payload = {};
        formData.forEach((value, key) => {
            if (key.endsWith('[]')) {
                const base = key.slice(0, -2);
                if (!Array.isArray(payload[base])) {
                    payload[base] = [];
                }
                payload[base].push(value);
            } else {
                payload[key] = value;
            }
        });
        if (typeof payload.image === 'string') {
            payload.image = payload.image.trim();
        }

        const recurrence = {
            frequency: payload.recurrence_frequency || 'none',
            interval: Number.parseInt(payload.recurrence_interval ?? '', 10) || 1,
            unit: payload.recurrence_unit || 'days',
            end_type: payload.recurrence_end_type || 'never',
            end_count: Number.parseInt(payload.recurrence_end_count ?? '', 10) || 0,
            end_date: (payload.recurrence_end_date || '').trim(),
        };
        payload.recurrence = recurrence;
        delete payload.recurrence_frequency;
        delete payload.recurrence_interval;
        delete payload.recurrence_unit;
        delete payload.recurrence_end_type;
        delete payload.recurrence_end_count;
        delete payload.recurrence_end_date;
        return payload;
    }

    function setupEditor(form) {
        const toolbar = form.querySelector('.events-editor-toolbar');
        const editor = form.querySelector('[data-events-editor]');
        const target = form.querySelector('[data-events-editor-target]');
        if (!toolbar || !editor || !target) {
            return;
        }
        toolbar.addEventListener('click', (event) => {
            const command = event.target.closest('[data-editor-command]')?.dataset.editorCommand;
            if (!command) {
                return;
            }
            event.preventDefault();
            document.execCommand(command, false, null);
            target.value = editor.innerHTML;
        });
        editor.addEventListener('input', () => {
            target.value = editor.innerHTML;
        });
    }

    function setupRecurrence(form) {
        if (!form) {
            return null;
        }
        if (form.__recurrenceControls) {
            return form.__recurrenceControls;
        }
        const controls = {
            frequency: form.querySelector('[data-events-recurrence-frequency]'),
            interval: form.querySelector('[data-events-recurrence-interval]'),
            unit: form.querySelector('[data-events-recurrence-unit]'),
            endType: Array.from(form.querySelectorAll('[data-events-recurrence-end]')),
            endCountGroup: form.querySelector('[data-events-recurrence-count-group]'),
            endCountFields: form.querySelector('[data-events-recurrence-count-fields]'),
            endDateGroup: form.querySelector('[data-events-recurrence-date-group]'),
            endCount: form.querySelector('[data-events-recurrence-count]'),
            endDate: form.querySelector('[data-events-recurrence-date]'),
            custom: form.querySelector('[data-events-recurrence-custom]'),
        };

        function sync() {
            const frequency = controls.frequency ? controls.frequency.value : 'none';
            const isCustom = frequency === 'custom';
            if (controls.custom) {
                controls.custom.hidden = !isCustom;
            }
            if (controls.interval) {
                controls.interval.disabled = !isCustom;
            }
            if (controls.unit) {
                controls.unit.disabled = !isCustom;
            }

            const selectedEnd = controls.endType.find((radio) => radio.checked)?.value || 'never';
            const isAfter = selectedEnd === 'after';
            const isOnDate = selectedEnd === 'on_date';

            if (controls.endCountGroup) {
                controls.endCountGroup.classList.toggle('is-active', isAfter);
            }
            if (controls.endCountFields) {
                controls.endCountFields.hidden = !isAfter;
            }
            if (controls.endCount) {
                controls.endCount.disabled = !isAfter;
                if (!isAfter) {
                    controls.endCount.value = controls.endCount.value || '';
                }
            }

            if (controls.endDateGroup) {
                controls.endDateGroup.classList.toggle('is-active', isOnDate);
            }
            if (controls.endDate) {
                controls.endDate.disabled = !isOnDate;
                controls.endDate.hidden = !isOnDate;
                if (!isOnDate) {
                    controls.endDate.value = controls.endDate.value || '';
                }
            }
        }

        controls.sync = sync;

        if (controls.frequency) {
            controls.frequency.addEventListener('change', sync);
        }
        controls.endType.forEach((radio) => {
            radio.addEventListener('change', sync);
        });
        form.addEventListener('reset', () => {
            setTimeout(sync, 0);
        });

        sync();
        form.__recurrenceControls = controls;
        return controls;
    }

    function handleEventForm() {
        const modal = selectors.modal;
        if (!modal) {
            return;
        }
        const form = modal.querySelector('[data-events-form="event"]');
        setupEditor(form);
        setupRecurrence(form);
        if (!form.__imagePicker) {
            form.__imagePicker = initImagePicker(form);
        }
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const payload = serializeForm(form);
            payload.tickets = gatherTickets(form.querySelector('[data-events-tickets]'));
            return fetchJSON('save_event', { method: 'POST', body: payload })
                .then((response) => {
                    if (response?.event?.id) {
                        state.events.set(response.event.id, response.event);
                    }
                    closeModal(selectors.modal);
                    showToast('Event saved successfully.');
                    refreshAll();
                })
                .catch(() => {
                    showToast('Unable to save event.', 'error');
                });
        });
        modal.addEventListener('click', (event) => {
            if (event.target.matches('[data-ticket-remove]')) {
                const row = event.target.closest('.events-ticket-row');
                if (row) {
                    row.remove();
                }
                const container = modal.querySelector('[data-events-tickets]');
                if (container && container.children.length === 0) {
                    container.innerHTML = '<div class="events-ticket-empty">No ticket types yet. Add one to begin selling.</div>';
                }
            }
        });
        const addTicketBtn = modal.querySelector('[data-events-ticket-add]');
        addTicketBtn.addEventListener('click', () => {
            addTicketRow(modal.querySelector('[data-events-tickets]'));
        });
    }

    function openEventModal(eventId) {
        const modal = selectors.modal;
        if (!modal) {
            return;
        }
        const title = modal.querySelector('.events-modal-title');
        title.textContent = eventId ? 'Edit event' : 'Create event';
        if (eventId) {
            fetchJSON('get_event', { params: { id: eventId } })
                .then((response) => {
                    fillEventForm(response.event || {});
                    openModal(modal.closest('.events-modal-backdrop'));
                })
                .catch(() => {
                    showToast('Unable to load event.', 'error');
                });
        } else {
            fillEventForm({});
            openModal(modal.closest('.events-modal-backdrop'));
        }
    }

    function openConfirm(message, onConfirm) {
        const modal = selectors.confirmModal;
        if (!modal) {
            return;
        }
        modal.querySelector('[data-events-confirm-message]').textContent = message;
        state.confirm = onConfirm;
        openModal(modal);
    }

    function attachConfirmHandler() {
        if (!selectors.confirmModal) {
            return;
        }
        selectors.confirmModal.querySelector('[data-events-confirm]').addEventListener('click', () => {
            if (typeof state.confirm === 'function') {
                state.confirm();
            }
            state.confirm = null;
            closeModal(selectors.confirmModal);
        });
    }

    function refreshOrders() {
        return fetchJSON('list_orders', { params: state.ordersFilter })
            .then((response) => {
                const orders = Array.isArray(response.orders) ? response.orders : [];
                state.orders = orders
                    .map((order) => normalizeOrderRow(order))
                    .filter((order) => order !== null);
                renderOrdersTable();
                updateInsightsAndMetrics();
            })
            .catch(() => {
                showToast('Unable to load orders.', 'error');
            });
    }

    function refreshEvents() {
        return fetchJSON('list_events')
            .then((response) => {
                state.eventRows = Array.isArray(response.events) ? response.events : [];
                response.events.forEach((row) => {
                    const existing = state.events.get(row.id);
                    if (existing) {
                        existing.status = row.status;
                        existing.categories = Array.isArray(row.categories) ? row.categories : [];
                        existing.image = row.image || '';
                        if (row.start) {
                            existing.start = row.start;
                        }
                        if (row.end !== undefined) {
                            existing.end = row.end;
                        }
                        if (row.next_start) {
                            existing.next_start = row.next_start;
                        }
                        if (row.next_end !== undefined) {
                            existing.next_end = row.next_end;
                        }
                        if (row.recurrence) {
                            existing.recurrence = row.recurrence;
                        }
                        if (row.recurrence_summary !== undefined) {
                            existing.recurrence_summary = row.recurrence_summary;
                        }
                    }
                });
                renderEventsTable();
                populateEventSelect(selectors.orders.filterEvent);
            })
            .catch(() => {
                showToast('Unable to load events.', 'error');
            });
    }

    function refreshOverview() {
        return fetchJSON('overview')
            .then((response) => {
                renderStats({
                    total_events: response.stats?.total_events,
                    total_tickets_sold: response.stats?.total_tickets_sold,
                    total_revenue: response.stats?.total_revenue,
                });
                const upcoming = Array.isArray(response.upcoming) ? response.upcoming : [];
                renderUpcoming(upcoming);
            })
            .catch(() => {
                showToast('Unable to refresh overview.', 'error');
            });
    }

    function refreshReportsSummary() {
        return fetchJSON('reports_summary')
            .then((response) => {
                const reports = Array.isArray(response.reports) ? response.reports : [];
                state.salesSummary = reports.map((report) => ({
                    ...report,
                    refunded: report.refunded ?? 0,
                }));
                renderReportsTable();
                updateInsightsAndMetrics();
            })
            .catch(() => {
                showToast('Unable to load reports data.', 'error');
            });
    }

    function refreshCategories() {
        return fetchJSON('list_categories')
            .then((response) => {
                if (Array.isArray(response.categories)) {
                    state.categories = sortCategories(response.categories);
                    renderCategoryList();
                    renderCategoryOptions(getSelectedCategoryIds());
                }
            })
            .catch(() => {
                showToast('Unable to load categories.', 'error');
            });
    }

    function refreshAll() {
        refreshEvents();
        refreshOrders();
        refreshOverview();
        refreshReportsSummary();
        refreshCategories();
    }

    function attachEventListeners() {
        if (selectors.filters.status) {
            selectors.filters.status.addEventListener('change', (event) => {
                state.filters.status = event.target.value;
                renderEventsTable();
            });
        }
        if (selectors.filters.search) {
            selectors.filters.search.addEventListener('input', (event) => {
                state.filters.search = event.target.value;
                renderEventsTable();
            });
        }
        root.addEventListener('click', (event) => {
            const action = event.target.closest('[data-events-action]');
            if (!action) {
                return;
            }
            const id = action.dataset.id;
            switch (action.dataset.eventsAction) {
                case 'edit':
                    openEventModal(id);
                    break;
                case 'sales':
                    document.getElementById('eventsOrdersTitle')?.scrollIntoView({ behavior: 'smooth' });
                    if (selectors.orders.filterEvent) {
                        selectors.orders.filterEvent.value = id;
                        state.ordersFilter.event = id;
                        refreshOrders();
                    }
                    break;
                case 'end':
                    openConfirm('End this event? It will move to the ended state.', () => {
                        fetchJSON('end_event', { method: 'POST', body: { id } })
                            .then(() => {
                                showToast('Event ended.');
                                refreshAll();
                            })
                            .catch(() => {
                                showToast('Unable to end event.', 'error');
                            });
                    });
                    break;
                case 'delete':
                    openConfirm('Delete this event? This cannot be undone.', () => {
                        fetchJSON('delete_event', { method: 'POST', body: { id } })
                            .then(() => {
                                state.events.delete(id);
                                showToast('Event deleted.');
                                refreshAll();
                            })
                            .catch(() => {
                                showToast('Unable to delete event.', 'error');
                            });
                    });
                    break;
                default:
                    break;
            }
        });
        root.addEventListener('click', (event) => {
            const manage = event.target.closest('[data-events-order-manage]');
            if (!manage) {
                return;
            }
            const id = manage.dataset.id;
            if (id) {
                openOrderModal(id);
            }
        });
        if (selectors.orders.filterEvent) {
            selectors.orders.filterEvent.addEventListener('change', (event) => {
                state.ordersFilter.event = event.target.value;
                refreshOrders();
            });
        }
        if (selectors.orders.filterStatus) {
            selectors.orders.filterStatus.addEventListener('change', (event) => {
                state.ordersFilter.status = event.target.value;
                refreshOrders();
            });
        }
        if (selectors.orders.exportBtn) {
            selectors.orders.exportBtn.addEventListener('click', () => {
                window.open(`${endpoint}?action=export_orders`, '_blank');
            });
        }
        const reportButtons = root.querySelectorAll('[data-events-report-download]');
        reportButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const type = button.dataset.eventsReportDownload;
                const rows = [['Event', 'Tickets Sold', 'Revenue', 'Refunded', 'Status']];
                state.salesSummary.forEach((item) => {
                    rows.push([
                        item.title,
                        item.tickets_sold,
                        item.revenue,
                        item.refunded ?? 0,
                        item.status,
                    ]);
                });
                const csv = rows.map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `events-${type}-report.csv`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(link.href);
            });
        });
    }

    function handleCategoryForm() {
        if (selectors.categoriesForm) {
            selectors.categoriesForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const formData = new FormData(selectors.categoriesForm);
                const payload = {
                    id: formData.get('id') || undefined,
                    name: String(formData.get('name') || '').trim(),
                    slug: String(formData.get('slug') || '').trim(),
                };
                if (!payload.name) {
                    showToast('Category name is required.', 'error');
                    return;
                }
                if (!payload.id) {
                    delete payload.id;
                }
                if (!payload.slug) {
                    delete payload.slug;
                }
                const isUpdate = Boolean(state.categoryEditing);
                fetchJSON('save_category', { method: 'POST', body: payload })
                    .then((response) => {
                        if (Array.isArray(response.categories)) {
                            state.categories = sortCategories(response.categories);
                        }
                        renderCategoryList();
                        renderCategoryOptions(getSelectedCategoryIds());
                        showToast(isUpdate ? 'Category updated.' : 'Category created.');
                        resetCategoryForm();
                    })
                    .catch(() => {
                        showToast('Unable to save category.', 'error');
                    });
            });
        }
        if (selectors.categoriesReset) {
            selectors.categoriesReset.addEventListener('click', (event) => {
                event.preventDefault();
                resetCategoryForm();
            });
        }
        if (selectors.categoriesModal) {
            selectors.categoriesModal.addEventListener('click', (event) => {
                const editBtn = event.target.closest('[data-events-category-edit]');
                if (editBtn) {
                    const category = state.categories.find((item) => String(item.id) === String(editBtn.dataset.id));
                    if (category) {
                        fillCategoryForm(category);
                        selectors.categoriesForm?.querySelector('[name="name"]').focus();
                    }
                    return;
                }
                const deleteBtn = event.target.closest('[data-events-category-delete]');
                if (deleteBtn) {
                    const id = deleteBtn.dataset.id;
                    openConfirm('Delete this category? It will be removed from any events.', () => {
                        fetchJSON('delete_category', { method: 'POST', body: { id } })
                            .then((response) => {
                                if (Array.isArray(response.categories)) {
                                    state.categories = sortCategories(response.categories);
                                } else {
                                    state.categories = [];
                                }
                                state.events.forEach((event) => {
                                    if (Array.isArray(event.categories)) {
                                        event.categories = event.categories.filter((categoryId) => String(categoryId) !== String(id));
                                    }
                                });
                                renderCategoryList();
                                renderCategoryOptions(getSelectedCategoryIds());
                                showToast('Category deleted.');
                                resetCategoryForm();
                            })
                            .catch(() => {
                                showToast('Unable to delete category.', 'error');
                            });
                    });
                }
            });
        }
    }

    function init() {
        bindModalDismissals();
        attachConfirmHandler();
        handleEventForm();
        handleOrderForm();
        handleCategoryForm();
        initMediaPicker();
        attachEventListeners();
        populateEventSelect(selectors.orders.filterEvent);
        renderEventsTable();
        renderOrdersTable();
        renderReportsTable();
        renderCategoryOptions();
        renderCategoryList();
        updateInsightsAndMetrics();
        refreshAll();
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-events-open]');
        if (!trigger) {
            return;
        }
        const type = trigger.dataset.eventsOpen;
        if (type === 'event') {
            openEventModal(null);
        } else if (type === 'categories') {
            openCategoriesModal();
        }
    });

    init();
})();
