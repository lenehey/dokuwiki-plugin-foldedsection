/* SPDX-License-Identifier: GPL-2.0-only */
(function () {
    'use strict';

    function initialize() {
        document.querySelectorAll('.dokuwiki a.foldedsection-toggle').forEach(function (toggle) {
            if (toggle.hasAttribute('data-foldedsection-ready')) return;
            var id = toggle.getAttribute('href').slice(1);
            var body = document.getElementById(id);
            if (!body) return;

            function setExpanded(expanded) {
                toggle.setAttribute('aria-expanded', String(expanded));
                body.hidden = !expanded;
            }

            toggle.addEventListener('click', function (event) {
                event.preventDefault();
                setExpanded(toggle.getAttribute('aria-expanded') !== 'true');
            });
            toggle.addEventListener('keydown', function (event) {
                if (event.key === ' ' || event.key === 'Spacebar') {
                    event.preventDefault();
                    toggle.click();
                }
            });
            toggle.setAttribute('role', 'button');
            toggle.setAttribute('aria-controls', id);
            toggle.setAttribute('data-foldedsection-ready', 'true');

            // Keep a section open when following a fragment into its content.
            var fragment = decodeFragment();
            var target = document.getElementById(fragment);
            setExpanded(fragment === id || (target !== null && body.contains(target)));
        });
    }

    function decodeFragment() {
        try { return decodeURIComponent(window.location.hash.slice(1)); }
        catch (error) { return ''; }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    window.addEventListener('hashchange', function () {
        var target = document.getElementById(decodeFragment());
        if (!target) return;
        document.querySelectorAll('.dokuwiki a.foldedsection-toggle[aria-controls]').forEach(function (toggle) {
            var body = document.getElementById(toggle.getAttribute('aria-controls'));
            if (body && (body === target || body.contains(target))) {
                body.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });
}());
