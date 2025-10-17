<?php
// File: modules/maps/view.php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/data.php';
require_once __DIR__ . '/helpers.php';

require_login();

maps_ensure_storage();

$locations = maps_read_locations();
$categories = maps_read_categories();

$totalLocations = count($locations);
$publishedLocations = 0;
foreach ($locations as $location) {
    $status = strtolower((string) ($location['status'] ?? 'draft'));
    if ($status === 'published') {
        $publishedLocations++;
    }
}
$draftLocations = max(0, $totalLocations - $publishedLocations);
$categoryCounts = maps_locations_grouped_by_category($locations);

$categorySummary = [];
foreach ($categories as $category) {
    $id = (string) ($category['id'] ?? '');
    if ($id === '') {
        continue;
    }
    $categorySummary[] = [
        'id' => $id,
        'name' => $category['name'] ?? 'Category',
        'color' => $category['color'] ?? '#666666',
        'count' => isset($categoryCounts[$id]) ? count($categoryCounts[$id]) : 0,
    ];
}
$categorySummary[] = [
    'id' => 'uncategorized',
    'name' => 'Uncategorized',
    'color' => '#9CA3AF',
    'count' => isset($categoryCounts['uncategorized']) ? count($categoryCounts['uncategorized']) : 0,
];

$initialPayload = [
    'locations' => $locations,
    'categories' => $categories,
];
$initialAttr = htmlspecialchars(json_encode($initialPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
?>
<div class="content-section maps-module" id="mapsModule" data-initial="<?php echo $initialAttr; ?>">
    <header class="maps-hero">
        <div class="maps-hero__content">
            <div>
                <span class="maps-hero__eyebrow">Location intelligence</span>
                <h2 class="maps-hero__title">Maps &amp; Locations</h2>
                <p class="maps-hero__subtitle">Curate a trusted directory of places your visitors can explore on your site and embedded maps.</p>
            </div>
            <div class="maps-hero__actions">
                <button type="button" class="maps-btn maps-btn--primary" id="mapAddLocationBtn">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span>Add location</span>
                </button>
                <button type="button" class="maps-btn maps-btn--ghost" id="mapRefreshBtn" aria-label="Refresh locations">
                    <i class="fas fa-rotate" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="maps-hero__stats">
            <div class="maps-stat-card">
                <div class="maps-stat-card__label">Total locations</div>
                <div class="maps-stat-card__value" data-map-stat="total"><?php echo $totalLocations; ?></div>
            </div>
            <div class="maps-stat-card">
                <div class="maps-stat-card__label">Published</div>
                <div class="maps-stat-card__value" data-map-stat="published"><?php echo $publishedLocations; ?></div>
            </div>
            <div class="maps-stat-card">
                <div class="maps-stat-card__label">Drafts</div>
                <div class="maps-stat-card__value" data-map-stat="draft"><?php echo $draftLocations; ?></div>
            </div>
            <div class="maps-stat-card">
                <div class="maps-stat-card__label">Categories</div>
                <div class="maps-stat-card__value" data-map-stat="categories"><?php echo count($categories); ?></div>
            </div>
        </div>
    </header>

    <section class="maps-workspace">
        <div class="maps-locations">
            <div class="maps-panel-header">
                <div>
                    <h3 class="maps-panel-title">Location manager</h3>
                    <p class="maps-panel-subtitle">Search, filter, and publish map-ready locations.</p>
                </div>
                <div class="maps-panel-actions">
                    <label class="maps-search" for="mapLocationSearch">
                        <i class="fas fa-search" aria-hidden="true"></i>
                        <input type="search" id="mapLocationSearch" placeholder="Search locations" aria-label="Search locations">
                    </label>
                    <select id="mapCategoryFilter" class="maps-select" aria-label="Filter by category">
                        <option value="">All categories</option>
                        <?php foreach ($categorySummary as $item): ?>
                        <option value="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="mapStatusFilter" class="maps-select" aria-label="Filter by status">
                        <option value="">All statuses</option>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div class="maps-table" aria-live="polite">
                <div class="maps-table__header">
                    <div>Name</div>
                    <div>Status</div>
                    <div>Location</div>
                    <div>Categories</div>
                    <div>Updated</div>
                    <div>Actions</div>
                </div>
                <div class="maps-table__body" id="mapLocationsTable"></div>
            </div>
            <div class="maps-empty" id="mapLocationsEmpty" <?php echo $totalLocations > 0 ? 'hidden' : ''; ?>>
                <i class="fas fa-map-location-dot" aria-hidden="true"></i>
                <h3>No locations yet</h3>
                <p>Start by adding your first place to power your interactive maps.</p>
                <button type="button" class="maps-btn maps-btn--primary" id="mapEmptyAddBtn">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span>Create location</span>
                </button>
            </div>
            <div class="maps-empty" id="mapLocationsNoResults" hidden>
                <i class="fas fa-filter" aria-hidden="true"></i>
                <h3>No matches found</h3>
                <p>Try adjusting the search term or filters.</p>
            </div>
        </div>

        <aside class="maps-categories">
            <div class="maps-panel-header">
                <div>
                    <h3 class="maps-panel-title">Categories</h3>
                    <p class="maps-panel-subtitle">Group locations for filtering and themed map layers.</p>
                </div>
                <button type="button" class="maps-btn maps-btn--secondary" id="mapAddCategoryBtn">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span>Add category</span>
                </button>
            </div>
            <ul class="maps-category-list" id="mapCategoryList" aria-live="polite">
                <?php foreach ($categorySummary as $item): ?>
                <li class="maps-category-item" data-category-id="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>">
                    <span class="maps-category-color" style="background-color: <?php echo htmlspecialchars($item['color'], ENT_QUOTES, 'UTF-8'); ?>"></span>
                    <span class="maps-category-name"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="maps-category-count" data-count="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo (int) $item['count']; ?></span>
                    <?php if ($item['id'] !== 'uncategorized'): ?>
                    <div class="maps-category-actions">
                        <button type="button" class="maps-icon-btn" data-map-edit-category="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" aria-label="Edit category">
                            <i class="fas fa-pen" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="maps-icon-btn" data-map-delete-category="<?php echo htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8'); ?>" aria-label="Delete category">
                            <i class="fas fa-trash" aria-hidden="true"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </section>

    <div class="maps-modal" id="mapLocationModal" role="dialog" aria-modal="true" aria-labelledby="mapLocationModalTitle" hidden>
        <div class="maps-modal__dialog">
            <header class="maps-modal__header">
                <div>
                    <h3 id="mapLocationModalTitle">Add location</h3>
                    <p class="maps-modal__subtitle">Provide details visitors will see in directory listings and map pins.</p>
                </div>
                <button type="button" class="maps-icon-btn" data-map-dismiss="mapLocationModal" aria-label="Close">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>
            <form id="mapLocationForm" class="maps-form">
                <input type="hidden" name="id" id="mapLocationId">
                <div class="maps-form__grid">
                    <label class="maps-form__field">
                        <span class="maps-form__label">Name</span>
                        <input type="text" name="name" id="mapLocationName" required>
                    </label>
                    <label class="maps-form__field" data-map-meta-field="slug" hidden>
                        <span class="maps-form__label">Slug</span>
                        <input type="text" name="slug" id="mapLocationSlug" placeholder="auto-generated if blank">
                    </label>
                    <label class="maps-form__field" data-map-meta-field="status" hidden>
                        <span class="maps-form__label">Status</span>
                        <select name="status" id="mapLocationStatus">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>
                    </label>
                    <label class="maps-form__field maps-form__field--full">
                        <span class="maps-form__label">Description</span>
                        <textarea name="description" id="mapLocationDescription" rows="4" placeholder="What makes this location special?"></textarea>
                    </label>
                    <fieldset class="maps-form__fieldset">
                        <legend>Address</legend>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Street</span>
                            <input type="text" name="address[street]" id="mapLocationStreet">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">City</span>
                            <input type="text" name="address[city]" id="mapLocationCity">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Region</span>
                            <input type="text" name="address[region]" id="mapLocationRegion">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Postal code</span>
                            <input type="text" name="address[postal_code]" id="mapLocationPostal">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Country</span>
                            <input type="text" name="address[country]" id="mapLocationCountry">
                        </label>
                    </fieldset>
                    <fieldset class="maps-form__fieldset">
                        <legend>Coordinates</legend>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Latitude</span>
                            <input type="text" name="coordinates[lat]" id="mapLocationLat" placeholder="e.g. 40.7128">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Longitude</span>
                            <input type="text" name="coordinates[lng]" id="mapLocationLng" placeholder="e.g. -74.0060">
                        </label>
                    </fieldset>
                    <p class="maps-form__hint" id="mapLocationGeocodeStatus" data-map-geocode-default="Latitude and longitude update automatically from the address." aria-live="polite">
                        Latitude and longitude update automatically from the address.
                    </p>
                    <fieldset class="maps-form__fieldset">
                        <legend>Contact</legend>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Phone</span>
                            <input type="text" name="contact[phone]" id="mapLocationPhone">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Email</span>
                            <input type="email" name="contact[email]" id="mapLocationEmail">
                        </label>
                        <label class="maps-form__field">
                            <span class="maps-form__label">Website</span>
                            <input type="url" name="contact[website]" id="mapLocationWebsite" placeholder="https://">
                        </label>
                    </fieldset>
                    <fieldset class="maps-form__fieldset maps-form__field--full">
                        <legend>Categories</legend>
                        <div id="mapLocationCategories" class="maps-chip-list" aria-live="polite"></div>
                    </fieldset>
                    <label class="maps-form__field maps-form__field--full">
                        <span class="maps-form__label">Tags</span>
                        <input type="text" name="tags" id="mapLocationTags" placeholder="Comma separated">
                    </label>
                    <label class="maps-form__field maps-form__field--full">
                        <span class="maps-form__label">Image IDs</span>
                        <input type="text" name="image_ids" id="mapLocationImages" placeholder="Comma separated image identifiers">
                    </label>
                    <label class="maps-form__field maps-form__field--full">
                        <span class="maps-form__label">Hours</span>
                        <textarea name="hours" id="mapLocationHours" rows="2" placeholder="Mon-Fri 9am-5pm"></textarea>
                    </label>
                    <label class="maps-form__field maps-form__field--full">
                        <span class="maps-form__label">Accessibility notes</span>
                        <textarea name="accessibility_notes" id="mapLocationAccessibility" rows="2" placeholder="Wheelchair access, parking details, etc."></textarea>
                    </label>
                </div>
                <div class="maps-form__actions">
                    <button type="submit" class="maps-btn maps-btn--primary" id="mapLocationSaveBtn">Save location</button>
                    <button type="button" class="maps-btn maps-btn--ghost" data-map-dismiss="mapLocationModal">Cancel</button>
                    <button type="button" class="maps-btn maps-btn--danger" id="mapLocationDeleteBtn" hidden>Delete</button>
                </div>
            </form>
        </div>
    </div>

    <div class="maps-modal" id="mapCategoryModal" role="dialog" aria-modal="true" aria-labelledby="mapCategoryModalTitle" hidden>
        <div class="maps-modal__dialog">
            <header class="maps-modal__header">
                <div>
                    <h3 id="mapCategoryModalTitle">Add category</h3>
                    <p class="maps-modal__subtitle">Organize locations into meaningful groups and map layers.</p>
                </div>
                <button type="button" class="maps-icon-btn" data-map-dismiss="mapCategoryModal" aria-label="Close">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </header>
            <form id="mapCategoryForm" class="maps-form">
                <input type="hidden" name="id" id="mapCategoryId">
                <label class="maps-form__field">
                    <span class="maps-form__label">Name</span>
                    <input type="text" name="name" id="mapCategoryName" required>
                </label>
                <label class="maps-form__field">
                    <span class="maps-form__label">Slug</span>
                    <input type="text" name="slug" id="mapCategorySlug" placeholder="auto-generated if blank">
                </label>
                <label class="maps-form__field">
                    <span class="maps-form__label">Icon (Font Awesome class)</span>
                    <input type="text" name="icon" id="mapCategoryIcon" placeholder="fa-location-dot">
                </label>
                <label class="maps-form__field">
                    <span class="maps-form__label">Color</span>
                    <input type="color" name="color" id="mapCategoryColor" value="#2D70F5">
                </label>
                <label class="maps-form__field">
                    <span class="maps-form__label">Sort order</span>
                    <input type="number" name="sort_order" id="mapCategorySort" min="0" step="1">
                </label>
                <div class="maps-form__actions">
                    <button type="submit" class="maps-btn maps-btn--primary">Save category</button>
                    <button type="button" class="maps-btn maps-btn--ghost" data-map-dismiss="mapCategoryModal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
