(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var body = document.body;
        var isConfigPage = body.classList.contains('toplevel_page_homepage') || body.classList.contains('appearance_page_homepage');
        if (!isConfigPage) {
            return;
        }

        var rows = Array.prototype.slice.call(document.querySelectorAll('.cmb2-options-page .cmb-row'));
        if (!rows.length) {
            return;
        }

        var titleRows = rows.filter(function (row) {
            return row.classList.contains('cmb-type-title');
        });

        titleRows.forEach(function (titleRow, sectionIndex) {
            titleRow.classList.add('is-open');
            titleRow.setAttribute('role', 'button');
            titleRow.setAttribute('tabindex', '0');
            titleRow.setAttribute('aria-expanded', 'true');

            var sectionRows = [];
            for (var i = rows.indexOf(titleRow) + 1; i < rows.length; i++) {
                if (rows[i].classList.contains('cmb-type-title')) {
                    break;
                }
                sectionRows.push(rows[i]);
            }

            var sectionWrapper = document.createElement('div');
            sectionWrapper.className = 'ev-section-fields';
            sectionRows.forEach(function (row) {
                sectionWrapper.appendChild(row);
            });
            titleRow.parentNode.insertBefore(sectionWrapper, titleRow.nextSibling);

            var setOpen = function (open) {
                titleRow.classList.toggle('is-open', open);
                titleRow.setAttribute('aria-expanded', open ? 'true' : 'false');
                sectionWrapper.classList.toggle('ev-section-hidden', !open);
            };

            var storageKey = 'ev_config_section_' + sectionIndex;
            var stored = window.sessionStorage.getItem(storageKey);
            if (stored === 'closed') {
                setOpen(false);
            }

            var toggleSection = function () {
                var isOpen = titleRow.classList.contains('is-open');
                setOpen(!isOpen);
                window.sessionStorage.setItem(storageKey, isOpen ? 'closed' : 'open');
            };

            titleRow.addEventListener('click', toggleSection);
            titleRow.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggleSection();
                }
            });
        });
    });
})();
