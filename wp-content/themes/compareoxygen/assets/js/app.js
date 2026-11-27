/* Compare Oxygen — progressive enhancement. No frameworks, no build step. */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------------------------------------------------------------
   * Sticky header state
   * ------------------------------------------------------------- */
  var header = document.querySelector('[data-site-header]');
  if (header) {
    var onScroll = function () {
      header.classList.toggle('is-stuck', window.scrollY > 8);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------------------------------------------------------------
   * Mobile navigation + submenu toggles
   * ------------------------------------------------------------- */
  var nav = document.querySelector('[data-primary-nav]');
  var menuToggle = document.querySelector('[data-menu-toggle]');
  var menuIcon = document.querySelector('[data-menu-icon]');

  function closeMenu() {
    if (!nav || !menuToggle) return;
    nav.classList.remove('is-open');
    menuToggle.setAttribute('aria-expanded', 'false');
    if (menuIcon) menuIcon.innerHTML = '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>';
  }

  if (nav && menuToggle) {
    menuToggle.addEventListener('click', function (event) {
      event.stopPropagation();
      var open = nav.classList.toggle('is-open');
      menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (menuIcon) {
        menuIcon.innerHTML = open
          ? '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6 6 18"/></svg>'
          : '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M4 7h16M4 12h16M4 17h16"/></svg>';
      }
    });

    nav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.matchMedia('(max-width: 980px)').matches) closeMenu();
      });
    });

    document.addEventListener('click', function (event) {
      if (!nav.classList.contains('is-open')) return;
      if (nav.contains(event.target) || menuToggle.contains(event.target)) return;
      closeMenu();
    });
  }

  // Add an explicit, keyboard-operable toggle to parents with submenus.
  document.querySelectorAll('.primary-nav .menu-item-has-children').forEach(function (item, index) {
    var link = item.querySelector(':scope > a');
    var submenu = item.querySelector(':scope > .submenu');
    if (!link || !submenu) return;
    if (!submenu.id) submenu.id = 'submenu-' + index;
    var toggle = document.createElement('button');
    toggle.type = 'button';
    toggle.className = 'nav-toggle';
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-controls', submenu.id);
    toggle.innerHTML = '<span class="screen-reader-text">' + (link.textContent || '').trim() + ' submenu</span>' +
      '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>';
    link.parentNode.insertBefore(toggle, link.nextSibling);
    toggle.addEventListener('click', function (event) {
      event.stopPropagation();
      var open = item.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    item.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        item.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });
  });

  /* ---------------------------------------------------------------
   * Search panel
   * ------------------------------------------------------------- */
  var searchToggle = document.querySelector('[data-search-toggle]');
  var searchPanel = document.querySelector('[data-search-panel]');
  var searchInput = document.getElementById('site-search-input');

  if (searchToggle && searchPanel) {
    var setSearch = function (open) {
      searchPanel.hidden = !open;
      searchPanel.classList.toggle('is-open', open);
      searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && searchInput) {
        window.setTimeout(function () { searchInput.focus(); }, 30);
      }
    };
    searchToggle.addEventListener('click', function () {
      setSearch(searchPanel.hidden);
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && !searchPanel.hidden) {
        setSearch(false);
        searchToggle.focus();
      }
    });
    document.addEventListener('click', function (event) {
      if (searchPanel.hidden) return;
      if (searchPanel.contains(event.target) || searchToggle.contains(event.target)) return;
      setSearch(false);
    });
  }

  /* ---------------------------------------------------------------
   * Compare combobox
   * ------------------------------------------------------------- */
  var dataNode = document.getElementById('coc-model-data');
  var compareForm = document.querySelector('[data-compare-form]');
  if (dataNode && compareForm) {
    var models = [];
    try { models = JSON.parse(dataNode.textContent || '[]'); } catch (e) { models = []; }

    var combos = [];

    function createCombo(root) {
      var input = root.querySelector('.combo__input');
      var hidden = root.querySelector('input[type="hidden"]');
      var listbox = root.querySelector('.combo__list');
      if (!input || !hidden || !listbox) return null;

      var combo = {
        root: root,
        input: input,
        hidden: hidden,
        listbox: listbox,
        activeIndex: -1,
        visible: [],
        selectedId: hidden.value ? Number(hidden.value) : 0,
        excludeId: 0
      };

      var selected = models.find(function (m) { return m.id === combo.selectedId; });
      if (selected) input.value = selected.name;

      function optionsForQuery(query) {
        var q = (query || '').trim().toLowerCase();
        return models.filter(function (model) {
          if (model.id === combo.excludeId) return false;
          if (!q) return true;
          return (model.name + ' ' + model.brand).toLowerCase().indexOf(q) !== -1;
        });
      }

      function render(query) {
        combo.visible = optionsForQuery(query);
        combo.activeIndex = combo.visible.length ? 0 : -1;
        combo.listbox.innerHTML = '';

        if (!combo.visible.length) {
          var empty = document.createElement('li');
          empty.className = 'combo__empty';
          empty.textContent = 'No matching concentrator. Try a brand or model name.';
          combo.listbox.appendChild(empty);
          return;
        }

        var currentBrand = '';
        combo.visible.forEach(function (model, index) {
          if ((query || '').trim() === '' && model.brand !== currentBrand) {
            currentBrand = model.brand;
            var group = document.createElement('li');
            group.className = 'combo__group';
            group.setAttribute('role', 'presentation');
            group.textContent = model.brand;
            combo.listbox.appendChild(group);
          }
          var option = document.createElement('li');
          option.className = 'combo__option' + (index === 0 ? ' is-active' : '');
          option.id = root.dataset.combo + '-opt-' + index;
          option.setAttribute('role', 'option');
          option.setAttribute('aria-selected', String(model.id === combo.selectedId));
          option.dataset.id = String(model.id);
          option.dataset.index = String(index);
          var name = document.createElement('span');
          name.className = 'combo__option-name';
          name.textContent = model.name;
          if (model.brand && (query || '').trim() !== '') {
            var small = document.createElement('small');
            small.textContent = model.brand;
            name.appendChild(small);
          }
          option.appendChild(name);
          if (model.weight) {
            var weight = document.createElement('span');
            weight.className = 'combo__option-weight';
            weight.textContent = model.weight;
            option.appendChild(weight);
          }
          combo.listbox.appendChild(option);
        });
      }

      function open() {
        render(combo.input.value === selectedName() ? '' : combo.input.value);
        combo.listbox.hidden = false;
        combo.input.setAttribute('aria-expanded', 'true');
      }

      function close() {
        combo.listbox.hidden = true;
        combo.input.setAttribute('aria-expanded', 'false');
        combo.input.value = selectedName();
        combo.activeIndex = -1;
      }

      function selectedName() {
        var selected = models.find(function (m) { return m.id === combo.selectedId; });
        return selected ? selected.name : '';
      }

      function highlight(index) {
        var options = combo.listbox.querySelectorAll('.combo__option');
        if (!options.length) return;
        index = Math.max(0, Math.min(options.length - 1, index));
        options.forEach(function (option) { option.classList.remove('is-active'); });
        options[index].classList.add('is-active');
        combo.activeIndex = index;
        var id = options[index].id;
        combo.input.setAttribute('aria-activedescendant', id);
        if (options[index].scrollIntoView) {
          options[index].scrollIntoView({ block: 'nearest' });
        }
      }

      function select(model) {
        combo.selectedId = model.id;
        combo.hidden.value = String(model.id);
        combo.input.value = model.name;
        close();
        document.dispatchEvent(new CustomEvent('coc:combo-selected', {
          detail: { id: model.id, combo: combo }
        }));
        if (compareForm) compareForm.dispatchEvent(new CustomEvent('coc:change'));
      }

      combo.input.addEventListener('focus', function () {
        combo.input.select();
        open();
      });

      combo.input.addEventListener('click', function () {
        if (combo.listbox.hidden) open();
      });

      combo.input.addEventListener('input', function () {
        combo.selectedId = 0;
        combo.hidden.value = '';
        combo.listbox.hidden = false;
        combo.input.setAttribute('aria-expanded', 'true');
        render(combo.input.value);
        if (compareForm) compareForm.dispatchEvent(new CustomEvent('coc:change'));
      });

      combo.input.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowDown') {
          event.preventDefault();
          if (combo.listbox.hidden) { open(); } else { highlight(combo.activeIndex + 1); }
        } else if (event.key === 'ArrowUp') {
          event.preventDefault();
          highlight(combo.activeIndex - 1);
        } else if (event.key === 'Enter') {
          if (!combo.listbox.hidden && combo.visible.length) {
            event.preventDefault();
            var model = combo.visible[Math.max(0, combo.activeIndex)];
            if (model) select(model);
          }
        } else if (event.key === 'Escape') {
          if (!combo.listbox.hidden) {
            event.preventDefault();
            close();
          }
        } else if (event.key === 'Tab') {
          close();
        }
      });

      combo.listbox.addEventListener('mousedown', function (event) {
        event.preventDefault();
      });

      combo.listbox.addEventListener('click', function (event) {
        var option = event.target.closest('.combo__option');
        if (!option) return;
        var model = models.find(function (m) { return m.id === Number(option.dataset.id); });
        if (model) select(model);
      });

      combo.listbox.addEventListener('mousemove', function (event) {
        var option = event.target.closest('.combo__option');
        if (!option) return;
        var options = Array.prototype.slice.call(combo.listbox.querySelectorAll('.combo__option'));
        highlight(options.indexOf(option));
      });

      document.addEventListener('coc:combo-selected', function (event) {
        if (!combos.length) return;
        var source = event.detail.combo;
        if (source === combo) return;
        // Remove the other combo's model from this list.
        combo.excludeId = 0;
        if (combo.selectedId && combo.selectedId === event.detail.id) {
          combo.selectedId = 0;
          combo.hidden.value = '';
          combo.input.value = '';
        }
        if (combo.listbox && !combo.listbox.hidden) render(combo.input.value);
      });

      // Native fallback support: keep hidden state in sync for pre-submit.
      combo.root.dataset.ready = 'true';
      return combo;
    }

    document.querySelectorAll('[data-combo]').forEach(function (root) {
      var combo = createCombo(root);
      if (combo) combos.push(combo);
    });

    // Cross-filter: exclude the model already chosen in the sibling combo.
    function syncExclusions() {
      var ids = combos.map(function (combo) { return combo.selectedId; });
      combos.forEach(function (combo, index) {
        var other = ids[1 - index] || 0;
        combo.excludeId = other;
      });
    }

    document.addEventListener('coc:combo-selected', function () {
      syncExclusions();
      hideError();
    });

    var errorBox = document.querySelector('[data-compare-error]');

    function showError(message) {
      if (!errorBox) return;
      errorBox.textContent = '';
      var icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
      icon.setAttribute('class', 'icon');
      icon.setAttribute('viewBox', '0 0 24 24');
      icon.setAttribute('fill', 'none');
      icon.setAttribute('stroke', 'currentColor');
      icon.setAttribute('stroke-width', '1.8');
      icon.setAttribute('aria-hidden', 'true');
      var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
      path.setAttribute('d', 'M12 3 2.5 20h19L12 3Zm0 6v5m0 3h.01');
      icon.appendChild(path);
      errorBox.appendChild(icon);
      errorBox.appendChild(document.createTextNode(message));
      errorBox.classList.add('is-visible');
      errorBox.setAttribute('role', 'alert');
    }

    function hideError() {
      if (!errorBox) return;
      errorBox.classList.remove('is-visible');
      errorBox.removeAttribute('role');
    }

    compareForm.addEventListener('coc:change', hideError);

    compareForm.addEventListener('submit', function (event) {
      if (!combos.length) return;
      var first = combos[0].hidden.value;
      var second = combos[1].hidden.value;
      if (!first || !second) {
        event.preventDefault();
        showError('Choose two concentrators before comparing.');
        (first ? combos[1] : combos[0]).input.focus();
        return;
      }
      if (first === second) {
        event.preventDefault();
        showError('Choose two different concentrators. The same model cannot be compared with itself.');
        combos[1].input.focus();
        return;
      }

      var modelA = null;
      var modelB = null;
      models.forEach(function (model) {
        if (model.id === Number(first)) modelA = model;
        if (model.id === Number(second)) modelB = model;
      });
      if (modelA && modelB && modelA.slug && modelB.slug) {
        event.preventDefault();
        var base = compareForm.getAttribute('action') || '/compare/';
        base = base.replace(/[?#].*$/, '').replace(/\/?$/, '/');
        window.location.assign(base + modelA.slug + '-vs-' + modelB.slug + '/');
      }
    });

    var swap = document.querySelector('[data-swap-models]');
    if (swap) {
      swap.addEventListener('click', function () {
        if (combos.length < 2) return;
        var first = combos[0];
        var second = combos[1];
        var a = first.hidden.value;
        var b = second.hidden.value;
        var nameA = first.input.value;
        var nameB = second.input.value;
        first.hidden.value = b;
        second.hidden.value = a;
        first.selectedId = Number(b) || 0;
        second.selectedId = Number(a) || 0;
        first.input.value = b ? nameB : '';
        second.input.value = a ? nameA : '';
        hideError();
      });
    }
  }

  /* ---------------------------------------------------------------
   * Comparison table controls
   * ------------------------------------------------------------- */
  var table = document.querySelector('[data-compare-table]');
  if (table) {
    var highlightButtons = Array.prototype.slice.call(document.querySelectorAll('[data-toggle-highlight]'));
    var diffButtons = Array.prototype.slice.call(document.querySelectorAll('[data-toggle-diff]'));
    var sourcesButtons = Array.prototype.slice.call(document.querySelectorAll('[data-toggle-sources]'));
    var tableRows = Array.prototype.slice.call(table.querySelectorAll('[data-spec-row]'));
    var tableGroups = Array.prototype.slice.call(table.querySelectorAll('[data-spec-group][id]'));
    var searchInput = document.querySelector('[data-compare-search]');
    var emptyMessage = document.querySelector('[data-compare-empty]');

    function setPressed(buttons, pressed) {
      buttons.forEach(function (button) {
        button.setAttribute('aria-pressed', pressed ? 'true' : 'false');
      });
    }

    function applyFilters() {
      var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
      var diffOnly = table.classList.contains('is-diffonly');
      var visibleGroups = {};
      var visibleRows = 0;

      tableRows.forEach(function (row) {
        var matchesQuery = !query || (row.getAttribute('data-spec-label') || '').toLowerCase().indexOf(query) !== -1;
        var matchesDiff = !diffOnly || row.getAttribute('data-diff') === 'true';
        var visible = matchesQuery && matchesDiff;
        row.classList.toggle('is-filtered', !visible);
        if (visible) {
          visibleRows++;
          visibleGroups[row.getAttribute('data-spec-group')] = true;
        }
      });

      tableGroups.forEach(function (group) {
        group.classList.toggle('is-filtered', !visibleGroups[group.getAttribute('data-spec-group')]);
      });
      if (emptyMessage) emptyMessage.hidden = visibleRows !== 0;
    }

    highlightButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var pressed = button.getAttribute('aria-pressed') === 'true';
        table.classList.toggle('no-highlight', pressed);
        setPressed(highlightButtons, !pressed);
      });
    });

    diffButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var pressed = button.getAttribute('aria-pressed') === 'true';
        table.classList.toggle('is-diffonly', !pressed);
        setPressed(diffButtons, !pressed);
        applyFilters();
      });
    });

    sourcesButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        var pressed = button.getAttribute('aria-pressed') === 'true';
        table.classList.toggle('sources-hidden', pressed);
        setPressed(sourcesButtons, !pressed);
      });
    });

    if (searchInput) {
      searchInput.addEventListener('input', applyFilters);
      searchInput.addEventListener('search', applyFilters);
    }
    applyFilters();
  }

  /* ---------------------------------------------------------------
   * Sticky comparison summary and section navigation
   * ------------------------------------------------------------- */
  (function () {
    var sticky = document.querySelector('[data-compare-sticky]');
    var products = document.querySelector('[data-compare-products]');
    var results = document.querySelector('.compare-results');
    if (!sticky || !products || !results) return;

    var sectionNav = document.querySelector('[data-compare-nav]');
    var navLinks = Array.prototype.slice.call(document.querySelectorAll('[data-compare-nav-link]'));
    var groups = Array.prototype.slice.call(results.querySelectorAll('[data-spec-group][id]'));
    var active = false;
    var ticking = false;

    function stickyHeight() {
      return sticky.offsetHeight || 0;
    }

    function headerHeight() {
      var siteHeader = document.querySelector('[data-site-header]') || document.querySelector('.site-header');
      return siteHeader ? Math.round(siteHeader.getBoundingClientRect().height) : 0;
    }

    function navHeight() {
      return sectionNav ? sectionNav.offsetHeight || 0 : 0;
    }

    function setSticky(on) {
      if (on === active) return;
      active = on;
      sticky.classList.toggle('is-active', on);
      sticky.setAttribute('aria-hidden', on ? 'false' : 'true');
      if (on) {
        sticky.removeAttribute('inert');
      } else {
        sticky.setAttribute('inert', '');
      }
      document.body.classList.toggle('compare-bar-on', on);
      updateActiveLink();
    }

    function updateActiveLink() {
      if (!navLinks.length || !groups.length) return;
      var offset = headerHeight() + navHeight() + (active ? stickyHeight() : 0) + 16;
      var current = groups[0];
      groups.forEach(function (group) {
        if (group.getBoundingClientRect().top <= offset) current = group;
      });
      var id = current.id;
      navLinks.forEach(function (link) {
        var on = link.getAttribute('href') === '#' + id;
        link.classList.toggle('is-active', on);
        if (on) link.setAttribute('aria-current', 'true');
        else link.removeAttribute('aria-current');
      });
    }

    function measure() {
      ticking = false;
      var header = headerHeight();
      var bar = stickyHeight();
      var resultRect = results.getBoundingClientRect();
      var navRect = sectionNav ? sectionNav.getBoundingClientRect() : null;
      // Activate exactly when the section nav reaches the line the bar occupies,
      // so the nav never jumps when the bar slides in above it.
      var navReached = navRect ? navRect.top <= header + bar : false;
      if (!sectionNav) {
        var productRect = products.getBoundingClientRect();
        var toolbar = results.querySelector('.compare-toolbar');
        navReached = productRect.bottom <= header && (!toolbar || toolbar.getBoundingClientRect().bottom <= header);
      }
      setSticky(navReached && resultRect.bottom > header + 8);
      updateActiveLink();
    }

    function requestMeasure() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(measure);
    }

    navLinks.forEach(function (link) {
      link.addEventListener('click', function (event) {
        var target = document.querySelector(link.getAttribute('href'));
        if (!target) return;
        event.preventDefault();
        // Keep the summary bar in view while the page scrolls to the section,
        // so the landing offset matches what the reader ends up seeing.
        var header = headerHeight();
        var productRect = products.getBoundingClientRect();
        var resultRect = results.getBoundingClientRect();
        var inTable = productRect.bottom <= header && resultRect.bottom > header + 8;
        var offset = header + (inTable ? stickyHeight() : 0) + navHeight() + 14;
        var top = target.getBoundingClientRect().top + window.scrollY - offset;
        window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' });
        if (window.history && window.history.replaceState) {
          window.history.replaceState(null, '', link.getAttribute('href'));
        }
      });
    });

    // The "add another product" tile is a native <details>; close its list when
    // the reader clicks elsewhere or presses Escape.
    var addPanels = Array.prototype.slice.call(document.querySelectorAll('[data-compare-add]'));
    if (addPanels.length) {
      document.addEventListener('click', function (event) {
        addPanels.forEach(function (panel) {
          if (panel.open && !panel.contains(event.target)) { panel.open = false; }
        });
      });
      document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
          addPanels.forEach(function (panel) { panel.open = false; });
        }
      });
    }

    window.addEventListener('scroll', requestMeasure, { passive: true });
    window.addEventListener('resize', requestMeasure);
    if (window.ResizeObserver) {
      new ResizeObserver(requestMeasure).observe(sticky);
    }
    measure();
  })();

  document.querySelectorAll('[data-share]').forEach(function (button) {
    button.addEventListener('click', function () {
      var url = button.dataset.share || window.location.href;
      var label = button.querySelector('[data-share-label]');
      var original = label ? label.textContent : '';
      var done = function () {
        if (!label) return;
        label.textContent = 'Link copied';
        window.setTimeout(function () { label.textContent = original; }, 2200);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done).catch(function () { window.prompt('Copy this link:', url); });
      } else {
        window.prompt('Copy this link:', url);
      }
    });
  });

  /* ---------------------------------------------------------------
   * Table of contents scroll spy
   * ------------------------------------------------------------- */
  var tocLinks = Array.prototype.slice.call(document.querySelectorAll('.toc a[href^="#"], .toc-mobile a[href^="#"]'));
  if (tocLinks.length && 'IntersectionObserver' in window) {
    var sections = tocLinks.map(function (link) {
      var id = link.getAttribute('href').slice(1);
      return document.getElementById(id);
    }).filter(Boolean);

    var setActive = function (id) {
      tocLinks.forEach(function (link) {
        var active = link.getAttribute('href') === '#' + id;
        link.classList.toggle('is-active', active);
        if (active) { link.setAttribute('aria-current', 'true'); } else { link.removeAttribute('aria-current'); }
      });
    };

    var observer = new IntersectionObserver(function (entries) {
      var visible = entries.filter(function (entry) { return entry.isIntersecting; });
      if (!visible.length) return;
      visible.sort(function (a, b) { return a.boundingClientRect.top - b.boundingClientRect.top; });
      setActive(visible[0].target.id);
    }, { rootMargin: '-15% 0px -70% 0px', threshold: 0 });

    sections.forEach(function (section) { observer.observe(section); });
  }

  /* ---------------------------------------------------------------
   * Reveal on scroll + hero tilt
   * ------------------------------------------------------------- */
  var revealTargets = document.querySelectorAll('[data-reveal]');
  if (revealTargets.length) {
    if (reduceMotion || !('IntersectionObserver' in window)) {
      revealTargets.forEach(function (el) { el.classList.add('is-visible'); });
    } else {
      var revealObserver = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
      revealTargets.forEach(function (el) { revealObserver.observe(el); });
    }
  }

  var visual = document.querySelector('.hero-visual');
  var card = document.querySelector('.hero-card');
  if (visual && card && !reduceMotion && window.matchMedia('(pointer: fine)').matches) {
    visual.addEventListener('pointermove', function (event) {
      var rect = visual.getBoundingClientRect();
      var x = (event.clientX - rect.left) / rect.width - 0.5;
      var y = (event.clientY - rect.top) / rect.height - 0.5;
      card.style.transform = 'rotateY(' + (x * 5).toFixed(2) + 'deg) rotateX(' + (-y * 4).toFixed(2) + 'deg)';
    });
    visual.addEventListener('pointerleave', function () {
      card.style.transform = 'rotateY(0deg) rotateX(0deg)';
    });
  }
})();
