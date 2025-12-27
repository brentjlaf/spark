// File: modules/fundraising/fundraising.js
$(function(){
    const $tabs = $('[data-fundraising-tabs]');
    const $panels = $('[data-fundraising-panel]');

    function activateTab(tabId) {
        if (!tabId) {
            return;
        }
        $tabs.find('[data-fundraising-tab]').each(function(){
            const $btn = $(this);
            const isActive = $btn.data('fundraising-tab') === tabId;
            $btn.toggleClass('is-active', isActive);
            $btn.attr('aria-selected', isActive ? 'true' : 'false');
        });

        $panels.each(function(){
            const $panel = $(this);
            const isActive = $panel.data('fundraising-panel') === tabId;
            $panel.toggleClass('is-active', isActive);
            if (isActive) {
                $panel.removeAttr('hidden');
            } else {
                $panel.attr('hidden', 'hidden');
            }
        });
    }

    $tabs.on('click', '[data-fundraising-tab]', function(){
        activateTab($(this).data('fundraising-tab'));
    });
});
