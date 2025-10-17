// File: modules/maps/maps.js
(function ($, window, document) {
    'use strict';

    var API_URL = 'modules/maps/api.php';
    var root = $('#mapsModule');
    if (!root.length) {
        return;
    }

    var state = {
        locations: [],
        categories: [],
        filters: {
            search: '',
            category: '',
            status: ''
        }
    };

    var initialData = root.attr('data-initial');
    if (initialData) {
        try {
            initialData = JSON.parse(initialData);
        } catch (error) {
            initialData = {};
        }
    } else {
        initialData = {};
    }

    bootstrap(initialData);
    bindEvents();
    render();

    function bootstrap(initial) {
        var locations = Array.isArray(initial.locations) ? initial.locations : [];
        var categories = Array.isArray(initial.categories) ? initial.categories : [];
        state.locations = locations.map(normalizeLocationSummary.bind(null, categories));
        var counts = computeCategoryCounts(locations);
        state.categories = categories.map(function (category) {
            return normalizeCategory(category, counts[category.id] || 0, false);
        });
        state.categories.push(normalizeCategory({
            id: 'uncategorized',
            name: 'Uncategorized',
            color: '#9CA3AF',
            icon: 'fa-circle-exclamation',
            is_default: true
        }, counts.uncategorized || 0, true));
    }

    function bindEvents() {
        root.on('input', '#mapLocationSearch', function () {
            state.filters.search = $(this).val().toLowerCase();
            renderLocations();
        });

        root.on('change', '#mapCategoryFilter', function () {
            state.filters.category = $(this).val();
            renderLocations();
        });

        root.on('change', '#mapStatusFilter', function () {
            state.filters.status = $(this).val();
            renderLocations();
        });

        root.on('click', '#mapAddLocationBtn, #mapEmptyAddBtn', function () {
            openLocationModal();
        });

        root.on('click', '#mapRefreshBtn', function () {
            refreshAll();
        });

        root.on('click', '[data-map-dismiss]', function () {
            var targetId = $(this).attr('data-map-dismiss');
            closeModal($('#' + targetId));
        });

        root.on('click', '.maps-modal', function (event) {
            if (event.target === this) {
                closeModal($(this));
            }
        });

        $(document).on('keydown.mapsModal', function (event) {
            if (event.key === 'Escape') {
                closeModal(root.find('.maps-modal:not([hidden])'));
            }
        });

        root.on('click', '[data-map-edit-location]', function () {
            var id = $(this).attr('data-map-edit-location');
            if (!id) {
                return;
            }
            fetchLocation(id).done(function (response) {
                if (response && response.location) {
                    openLocationModal(response.location);
                }
            });
        });

        root.on('click', '#mapLocationDeleteBtn', function () {
            var id = $('#mapLocationId').val();
            if (!id) {
                closeModal($('#mapLocationModal'));
                return;
            }
            if (!window.confirm('Delete this location? This action cannot be undone.')) {
                return;
            }
            apiRequest('delete_location', { id: id }).done(function (response) {
                if (response && Array.isArray(response.locations)) {
                    state.locations = response.locations;
                    refreshCategories();
                    render();
                    notify('Location deleted.', 'success');
                    closeModal($('#mapLocationModal'));
                }
            }).fail(handleAjaxError);
        });

        root.on('submit', '#mapLocationForm', function (event) {
            event.preventDefault();
            var payload = gatherLocationForm();
            apiRequest('save_location', payload).done(function (response) {
                if (response && Array.isArray(response.locations)) {
                    state.locations = response.locations;
                    refreshCategories();
                    render();
                    closeModal($('#mapLocationModal'));
                    notify('Location saved.', 'success');
                }
            }).fail(handleAjaxError);
        });

        root.on('click', '#mapAddCategoryBtn', function () {
            openCategoryModal();
        });

        root.on('click', '[data-map-edit-category]', function () {
            var id = $(this).attr('data-map-edit-category');
            var category = findCategory(id);
            if (category && !category.is_virtual) {
                openCategoryModal(category);
            }
        });

        root.on('click', '[data-map-delete-category]', function () {
            var id = $(this).attr('data-map-delete-category');
            var category = findCategory(id);
            if (!category || category.is_virtual) {
                return;
            }
            if (!window.confirm('Delete the "' + category.name + '" category?')) {
                return;
            }
            apiRequest('delete_category', { id: id }).done(function (response) {
                if (response && Array.isArray(response.categories)) {
                    updateCategories(response.categories);
                    updateCategoryFilter();
                    render();
                    notify('Category deleted.', 'success');
                }
            }).fail(handleAjaxError);
        });

        root.on('submit', '#mapCategoryForm', function (event) {
            event.preventDefault();
            var payload = gatherCategoryForm();
            apiRequest('save_category', payload).done(function (response) {
                if (response && Array.isArray(response.categories)) {
                    updateCategories(response.categories);
                    updateCategoryFilter();
                    render();
                    closeModal($('#mapCategoryModal'));
                    notify('Category saved.', 'success');
                }
            }).fail(handleAjaxError);
        });
    }

    function normalizeLocationSummary(categories, location) {
        categories = Array.isArray(categories) ? categories : [];
        var lookup = {};
        categories.forEach(function (category) {
            if (!category || !category.id) {
                return;
            }
            lookup[category.id] = category;
        });
        var locationCategories = [];
        if (location && Array.isArray(location.category_ids)) {
            location.category_ids.forEach(function (id) {
                if (lookup[id]) {
                    locationCategories.push({
                        id: id,
                        name: lookup[id].name || 'Category',
                        color: lookup[id].color || '#666666',
                        icon: lookup[id].icon || 'fa-location-dot'
                    });
                }
            });
        }
        return {
            id: location.id,
            name: location.name || 'Untitled location',
            slug: location.slug || '',
            status: location.status || 'draft',
            city: location.address && location.address.city ? location.address.city : '',
            region: location.address && location.address.region ? location.address.region : '',
            updated_at: location.updated_at || location.created_at || '',
            categories: locationCategories
        };
    }

    function computeCategoryCounts(locations) {
        var counts = {};
        if (!Array.isArray(locations)) {
            return counts;
        }
        locations.forEach(function (location) {
            var assigned = false;
            if (location && Array.isArray(location.category_ids)) {
                location.category_ids.forEach(function (id) {
                    if (!id) {
                        return;
                    }
                    assigned = true;
                    counts[id] = (counts[id] || 0) + 1;
                });
            }
            if (!assigned) {
                counts.uncategorized = (counts.uncategorized || 0) + 1;
            }
        });
        return counts;
    }

    function normalizeCategory(category, count, isVirtual) {
        category = category || {};
        return {
            id: category.id || '',
            name: category.name || 'Category',
            slug: category.slug || '',
            color: category.color || '#666666',
            icon: category.icon || 'fa-location-dot',
            sort_order: typeof category.sort_order === 'number' ? category.sort_order : 0,
            is_default: !!category.is_default,
            count: count || 0,
            is_virtual: !!isVirtual
        };
    }

    function render() {
        renderLocations();
        renderStats();
        renderCategorySidebar();
        updateCategoryChips();
    }

    function renderLocations() {
        var container = root.find('#mapLocationsTable');
        var emptyState = root.find('#mapLocationsEmpty');
        var noResults = root.find('#mapLocationsNoResults');
        var filtered = filterLocations();

        container.empty();

        if (!state.locations.length) {
            emptyState.removeAttr('hidden');
            noResults.attr('hidden', 'hidden');
            return;
        }
        emptyState.attr('hidden', 'hidden');

        if (!filtered.length) {
            noResults.removeAttr('hidden');
            return;
        }
        noResults.attr('hidden', 'hidden');

        filtered.forEach(function (location) {
            container.append(buildLocationRow(location));
        });
    }

    function filterLocations() {
        return state.locations.filter(function (location) {
            var matchesSearch = true;
            if (state.filters.search) {
                var haystack = [location.name, location.slug, location.city, location.region].join(' ').toLowerCase();
                matchesSearch = haystack.indexOf(state.filters.search) !== -1;
            }
            if (!matchesSearch) {
                return false;
            }
            if (state.filters.category) {
                if (state.filters.category === 'uncategorized') {
                    if (location.categories.length) {
                        return false;
                    }
                } else {
                    var hasCategory = location.categories.some(function (category) {
                        return category.id === state.filters.category;
                    });
                    if (!hasCategory) {
                        return false;
                    }
                }
            }
            if (state.filters.status) {
                if (location.status !== state.filters.status) {
                    return false;
                }
            }
            return true;
        });
    }

    function buildLocationRow(location) {
        var row = $('<div class="maps-table__row"></div>');
        var statusBadge = $('<span class="maps-status maps-status--' + location.status + '"></span>').text(capitalize(location.status));
        var cityRegion = $.trim([location.city, location.region].filter(Boolean).join(', '));
        var updated = location.updated_at ? formatRelativeTime(location.updated_at) : '—';
        var categories = $('<div class="maps-category-chips"></div>');
        if (location.categories.length) {
            location.categories.forEach(function (category) {
                var chip = $('<span class="maps-category-chip"></span>');
                chip.css('border-color', category.color || '#666666');
                chip.css('color', category.color || '#666666');
                chip.text(category.name);
                categories.append(chip);
            });
        } else {
            categories.append('<span class="maps-category-chip maps-category-chip--muted">Uncategorized</span>');
        }

        row.append($('<div class="maps-table__cell maps-table__cell--primary"></div>').append(
            $('<div class="maps-location-name"></div>').text(location.name),
            $('<div class="maps-location-slug"></div>').text(location.slug ? '/' + location.slug : '—')
        ));
        row.append($('<div class="maps-table__cell"></div>').append(statusBadge));
        row.append($('<div class="maps-table__cell"></div>').text(cityRegion || '—'));
        row.append($('<div class="maps-table__cell"></div>').append(categories));
        row.append($('<div class="maps-table__cell"></div>').text(updated));
        var actions = $('<div class="maps-table__cell maps-table__actions"></div>');
        actions.append('<button type="button" class="maps-icon-btn" data-map-edit-location="' + location.id + '" aria-label="Edit"><i class="fas fa-pen"></i></button>');
        row.append(actions);
        return row;
    }

    function renderStats() {
        var total = state.locations.length;
        var published = state.locations.filter(function (location) {
            return location.status === 'published';
        }).length;
        var draft = total - published;
        root.find('[data-map-stat="total"]').text(total);
        root.find('[data-map-stat="published"]').text(published);
        root.find('[data-map-stat="draft"]').text(draft);
        root.find('[data-map-stat="categories"]').text(state.categories.filter(function (category) {
            return !category.is_virtual;
        }).length);
    }

    function renderCategorySidebar() {
        var list = root.find('#mapCategoryList');
        list.empty();
        state.categories.forEach(function (category) {
            var item = $('<li class="maps-category-item"></li>').attr('data-category-id', category.id);
            var color = $('<span class="maps-category-color"></span>').css('background-color', category.color);
            var name = $('<span class="maps-category-name"></span>').text(category.name);
            var count = $('<span class="maps-category-count"></span>').attr('data-count', category.id).text(category.count);
            item.append(color, name, count);
            if (!category.is_virtual) {
                var actions = $('<div class="maps-category-actions"></div>');
                actions.append('<button type="button" class="maps-icon-btn" data-map-edit-category="' + category.id + '" aria-label="Edit category"><i class="fas fa-pen"></i></button>');
                actions.append('<button type="button" class="maps-icon-btn" data-map-delete-category="' + category.id + '" aria-label="Delete category"><i class="fas fa-trash"></i></button>');
                item.append(actions);
            }
            list.append(item);
        });
    }

    function updateCategoryChips() {
        var container = root.find('#mapLocationCategories');
        var selected = container.find('input[type="checkbox"]').filter(function () {
            return this.checked;
        }).map(function () {
            return $(this).val();
        }).get();
        container.empty();
        state.categories.forEach(function (category) {
            if (category.is_virtual) {
                return;
            }
            var id = 'mapCat_' + category.id;
            var wrapper = $('<label class="maps-chip"></label>').attr('for', id);
            var checkbox = $('<input type="checkbox" class="maps-chip__input">').attr('id', id).attr('value', category.id);
            checkbox.prop('checked', selected.indexOf(category.id) !== -1);
            wrapper.append(checkbox);
            var chip = $('<span class="maps-chip__label"></span>').text(category.name);
            wrapper.append(chip);
            container.append(wrapper);
        });
    }

    function updateCategoryFilter() {
        var select = root.find('#mapCategoryFilter');
        var current = select.val();
        select.empty();
        select.append('<option value="">All categories</option>');
        state.categories.forEach(function (category) {
            select.append('<option value="' + category.id + '">' + category.name + '</option>');
        });
        select.val(current || '');
    }

    function openLocationModal(location) {
        var modal = $('#mapLocationModal');
        resetLocationForm();
        if (location) {
            $('#mapLocationId').val(location.id || '');
            $('#mapLocationName').val(location.name || '');
            $('#mapLocationSlug').val(location.slug || '');
            $('#mapLocationStatus').val(location.status || 'draft');
            $('#mapLocationDescription').val(location.description || '');
            if (location.address) {
                $('#mapLocationStreet').val(location.address.street || '');
                $('#mapLocationCity').val(location.address.city || '');
                $('#mapLocationRegion').val(location.address.region || '');
                $('#mapLocationPostal').val(location.address.postal_code || '');
                $('#mapLocationCountry').val(location.address.country || '');
            }
            if (location.coordinates) {
                $('#mapLocationLat').val(location.coordinates.lat || '');
                $('#mapLocationLng').val(location.coordinates.lng || '');
            }
            if (location.contact) {
                $('#mapLocationPhone').val(location.contact.phone || '');
                $('#mapLocationEmail').val(location.contact.email || '');
                $('#mapLocationWebsite').val(location.contact.website || '');
            }
            if (Array.isArray(location.category_ids)) {
                location.category_ids.forEach(function (id) {
                    $('#mapLocationCategories input[value="' + id + '"]').prop('checked', true);
                });
            }
            if (Array.isArray(location.tags)) {
                $('#mapLocationTags').val(location.tags.join(', '));
            } else {
                $('#mapLocationTags').val(location.tags || '');
            }
            if (Array.isArray(location.image_ids)) {
                $('#mapLocationImages').val(location.image_ids.join(', '));
            } else {
                $('#mapLocationImages').val(location.image_ids || '');
            }
            $('#mapLocationHours').val(location.hours || '');
            $('#mapLocationAccessibility').val(location.accessibility_notes || '');
            $('#mapLocationModalTitle').text('Edit location');
            $('#mapLocationDeleteBtn').removeAttr('hidden');
        } else {
            $('#mapLocationModalTitle').text('Add location');
            $('#mapLocationDeleteBtn').attr('hidden', 'hidden');
        }
        openModal(modal);
    }

    function resetLocationForm() {
        var form = $('#mapLocationForm')[0];
        if (form && typeof form.reset === 'function') {
            form.reset();
        }
        $('#mapLocationCategories').find('input[type="checkbox"]').prop('checked', false);
    }

    function openCategoryModal(category) {
        var modal = $('#mapCategoryModal');
        var form = $('#mapCategoryForm')[0];
        if (form && typeof form.reset === 'function') {
            form.reset();
        }
        if (category) {
            $('#mapCategoryId').val(category.id || '');
            $('#mapCategoryName').val(category.name || '');
            $('#mapCategorySlug').val(category.slug || '');
            $('#mapCategoryIcon').val(category.icon || '');
            $('#mapCategoryColor').val(category.color || '#2D70F5');
            $('#mapCategorySort').val(category.sort_order || 0);
            $('#mapCategoryModalTitle').text('Edit category');
        } else {
            $('#mapCategoryModalTitle').text('Add category');
        }
        openModal(modal);
    }

    function openModal(modal) {
        modal.removeAttr('hidden').addClass('maps-modal--visible');
        setTimeout(function () {
            modal.find('input, textarea, select').filter(':visible:first').trigger('focus');
        }, 150);
    }

    function closeModal(modal) {
        if (!modal || !modal.length) {
            return;
        }
        modal.removeClass('maps-modal--visible');
        modal.attr('hidden', 'hidden');
    }

    function gatherLocationForm() {
        var categories = [];
        $('#mapLocationCategories input[type="checkbox"]').each(function () {
            if (this.checked) {
                categories.push($(this).val());
            }
        });
        return {
            id: $('#mapLocationId').val() || undefined,
            name: $('#mapLocationName').val(),
            slug: $('#mapLocationSlug').val(),
            status: $('#mapLocationStatus').val(),
            description: $('#mapLocationDescription').val(),
            address: {
                street: $('#mapLocationStreet').val(),
                city: $('#mapLocationCity').val(),
                region: $('#mapLocationRegion').val(),
                postal_code: $('#mapLocationPostal').val(),
                country: $('#mapLocationCountry').val()
            },
            coordinates: {
                lat: $('#mapLocationLat').val(),
                lng: $('#mapLocationLng').val()
            },
            contact: {
                phone: $('#mapLocationPhone').val(),
                email: $('#mapLocationEmail').val(),
                website: $('#mapLocationWebsite').val()
            },
            category_ids: categories,
            tags: $('#mapLocationTags').val(),
            image_ids: $('#mapLocationImages').val(),
            hours: $('#mapLocationHours').val(),
            accessibility_notes: $('#mapLocationAccessibility').val()
        };
    }

    function gatherCategoryForm() {
        return {
            id: $('#mapCategoryId').val() || undefined,
            name: $('#mapCategoryName').val(),
            slug: $('#mapCategorySlug').val(),
            icon: $('#mapCategoryIcon').val(),
            color: $('#mapCategoryColor').val(),
            sort_order: $('#mapCategorySort').val()
        };
    }

    function fetchLocation(id) {
        return $.ajax({
            url: API_URL,
            data: { action: 'get_location', id: id },
            method: 'GET',
            dataType: 'json'
        }).fail(handleAjaxError);
    }

    function refreshAll() {
        refreshLocations();
        refreshCategories();
    }

    function refreshLocations() {
        $.ajax({
            url: API_URL,
            data: { action: 'list_locations' },
            method: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (response && Array.isArray(response.locations)) {
                state.locations = response.locations;
                render();
                notify('Locations refreshed.', 'info');
            }
        }).fail(handleAjaxError);
    }

    function refreshCategories() {
        $.ajax({
            url: API_URL,
            data: { action: 'list_categories' },
            method: 'GET',
            dataType: 'json'
        }).done(function (response) {
            if (response && Array.isArray(response.categories)) {
                updateCategories(response.categories);
                updateCategoryFilter();
                render();
            }
        }).fail(handleAjaxError);
    }

    function updateCategories(categories) {
        state.categories = [];
        categories.forEach(function (category) {
            state.categories.push(normalizeCategory(category, category.count || 0, !!category.is_virtual));
        });
        var hasVirtual = state.categories.some(function (category) { return category.id === 'uncategorized'; });
        if (!hasVirtual) {
            state.categories.push(normalizeCategory({
                id: 'uncategorized',
                name: 'Uncategorized',
                color: '#9CA3AF',
                icon: 'fa-circle-exclamation',
                is_default: true
            }, 0, true));
        }
    }

    function apiRequest(action, payload) {
        return $.ajax({
            url: API_URL + '?action=' + encodeURIComponent(action),
            method: 'POST',
            data: JSON.stringify(payload || {}),
            contentType: 'application/json',
            dataType: 'json'
        });
    }

    function findCategory(id) {
        return state.categories.find(function (category) {
            return category.id === id;
        });
    }

    function notify(message, type) {
        if (window.AdminNotifications && typeof window.AdminNotifications.showToast === 'function') {
            var toastType = type || 'info';
            if (toastType === 'success' && window.AdminNotifications.showSuccessToast) {
                window.AdminNotifications.showSuccessToast(message);
                return;
            }
            if (toastType === 'error' && window.AdminNotifications.showErrorToast) {
                window.AdminNotifications.showErrorToast(message);
                return;
            }
            window.AdminNotifications.showToast(message, { type: toastType });
        }
    }

    function handleAjaxError(jqXHR) {
        var message = 'An unexpected error occurred.';
        if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.error) {
            message = jqXHR.responseJSON.error;
        }
        notify(message, 'error');
    }

    function formatRelativeTime(value) {
        if (!value) {
            return '—';
        }
        var date = new Date(value);
        if (isNaN(date.getTime())) {
            return value;
        }
        var diff = Date.now() - date.getTime();
        var minute = 60 * 1000;
        var hour = 60 * minute;
        var day = 24 * hour;
        if (diff < minute) {
            return 'Just now';
        }
        if (diff < hour) {
            var minutes = Math.round(diff / minute);
            return minutes + ' minute' + (minutes === 1 ? '' : 's') + ' ago';
        }
        if (diff < day) {
            var hours = Math.round(diff / hour);
            return hours + ' hour' + (hours === 1 ? '' : 's') + ' ago';
        }
        var days = Math.round(diff / day);
        if (days <= 7) {
            return days + ' day' + (days === 1 ? '' : 's') + ' ago';
        }
        return date.toLocaleDateString();
    }

    function capitalize(value) {
        if (!value) {
            return '';
        }
        return value.charAt(0).toUpperCase() + value.slice(1);
    }
})(jQuery, window, document);
