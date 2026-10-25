(() => {
    'use strict';
    document.querySelectorAll('[data-landing-editor]').forEach(form => {
        const builder = form.matches('[data-region-builder]');
        const tabs = [...form.querySelectorAll('[data-builder-tab]')];
        const panels = [...form.querySelectorAll('[data-builder-panel]')];
        let active = 0, dirty = false, submitted = false;
        const selectPanel = (index, focus = false) => {
            active = Math.max(0, Math.min(index, tabs.length - 1));
            tabs.forEach((tab, n) => {
                tab.setAttribute('aria-selected', String(n === active));
                tab.tabIndex = n === active ? 0 : -1;
            });
            panels.forEach((panel, n) => { panel.hidden = n !== active; });
            form.querySelector('[data-builder-prev]').disabled = active === 0;
            form.querySelector('[data-builder-next]').disabled = active === tabs.length - 1;
            if (focus) tabs[active].focus();
        };
        if (builder) {
            const cityScenes = form.querySelector('[data-city-scenes]');
            if (cityScenes) {
                const cityImage = form.querySelector('[name="location_image"]');
                const cityAlt = form.querySelector('[name="location_image_alt"]');
                const cityUpload = form.querySelector('[name="location_image_upload"]');
                const cityChoices = [...cityScenes.querySelectorAll('[data-city-scene]')];
                const cityClear = cityScenes.querySelector('[data-city-scene-clear]');
                const cityStatus = cityScenes.querySelector('[data-city-scene-status]');
                if (cityImage && cityAlt) {
                    const refreshCityScene = () => cityChoices.forEach(button => {
                        button.setAttribute('aria-pressed', String(!cityUpload?.files?.length && cityImage.value.trim() === button.dataset.cityScene));
                    });
                    const setCityScene = (path, alt, message) => {
                        // A pending native upload otherwise takes precedence over the chosen path.
                        if (cityUpload) cityUpload.value = '';
                        cityImage.value = path;
                        cityAlt.value = alt;
                        [cityImage, cityAlt].forEach(input => {
                            input.dispatchEvent(new Event('input', {bubbles: true}));
                            input.dispatchEvent(new Event('change', {bubbles: true}));
                        });
                        refreshCityScene();
                        cityStatus.textContent = message;
                    };
                    cityChoices.forEach(button => {
                        button.disabled = false;
                        button.addEventListener('click', () => setCityScene(button.dataset.cityScene, button.dataset.citySceneAlt, button.dataset.citySceneName + ' temsili görseli seçildi. Yayına uygulamak için değişiklikleri kaydedin.'));
                    });
                    if (cityClear) {
                        cityClear.disabled = false;
                        cityClear.addEventListener('click', () => setCityScene('', '', 'Şehir görseli kaldırıldı. Kaydettikten sonra görselsiz düzen kullanılır.'));
                    }
                    cityImage.addEventListener('input', refreshCityScene);
                    cityUpload?.addEventListener('change', () => {
                        refreshCityScene();
                        if (cityUpload.files.length) cityStatus.textContent = 'Yüklediğiniz dosya, kaydettikten sonra şehir görseli olarak kullanılacak. Açıklayıcı alt metni kontrol edin.';
                    });
                    refreshCityScene();
                }
            }
            const preview = form.querySelector('[data-theme-preview-dialog]');
            let previewOpener;
            if (preview) {
                const frame = preview.querySelector('iframe');
                form.querySelectorAll('[data-theme-preview]').forEach(button => button.addEventListener('click', () => {
                    if (!button.dataset.previewUrl) return;
                    previewOpener = button;
                    preview.dataset.device = button.dataset.themePreview;
                    frame.src = button.dataset.previewUrl;
                    preview.showModal();
                    preview.querySelector('[data-preview-close]').focus();
                }));
                preview.querySelector('[data-preview-close]').addEventListener('click', () => preview.close());
                preview.addEventListener('close', () => { frame.removeAttribute('src'); previewOpener?.focus(); });
                preview.addEventListener('click', event => { if (event.target === preview) preview.close(); });
            }
            form.classList.add('region-builder-ready');
            const nav = form.querySelector('[data-builder-tabs]');
            nav.setAttribute('role', 'tablist');
            nav.setAttribute('aria-orientation', 'vertical');
            tabs.forEach((tab, index) => {
                tab.setAttribute('role', 'tab');
                tab.setAttribute('aria-controls', panels[index].id);
                panels[index].setAttribute('role', 'tabpanel');
                tab.addEventListener('click', event => { event.preventDefault(); selectPanel(index); });
                tab.addEventListener('keydown', event => {
                    const next = {ArrowDown: active + 1, ArrowRight: active + 1, ArrowUp: active - 1, ArrowLeft: active - 1, Home: 0, End: tabs.length - 1}[event.key];
                    if (next !== undefined) { event.preventDefault(); selectPanel(next, true); }
                });
            });
            form.querySelector('[data-builder-prev]').addEventListener('click', () => selectPanel(active - 1, true));
            form.querySelector('[data-builder-next]').addEventListener('click', () => selectPanel(active + 1, true));
            selectPanel(0);
            form.querySelectorAll('[data-content-row]').forEach((row, index) => {
                row.hidden = index > 0 && ![...row.querySelectorAll('input,textarea')].some(input => input.value.trim());
            });
            form.addEventListener('invalid', event => {
                const panel = event.target.closest('[data-builder-panel]');
                if (panel) selectPanel(panels.indexOf(panel));
                for (let parent = event.target.parentElement; parent && parent !== form; parent = parent.parentElement) if (parent.tagName === 'DETAILS') parent.open = true;
            }, true);
            form.addEventListener('input', event => {
                if (!event.target.name) return;
                dirty = true;
                form.querySelector('[data-builder-state]').textContent = 'Kaydedilmemiş değişiklikler';
                const title = form.querySelector('[name="title"]').value.trim();
                form.querySelector('[data-builder-name]').textContent = title || 'Başlığınızı ekleyerek başlayın';
                form.querySelector('[data-design-title]').textContent = title || 'Şehriniz için güçlü bir başlangıç.';
            });
            form.addEventListener('submit', () => { submitted = true; });
            window.addEventListener('beforeunload', event => {
                if (dirty && !submitted) { event.preventDefault(); event.returnValue = ''; }
            });
        }

        form.querySelectorAll('[data-content-picker]').forEach(picker => {
            const selected = picker.querySelector('[data-picker-selected]');
            const available = picker.querySelector('[data-picker-available]');
            const search = picker.querySelector('[data-picker-search]');
            const normalize = value => value.toLocaleLowerCase('tr').normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            const refresh = () => {
                const count = selected.children.length;
                picker.querySelector('[data-picker-count]').textContent = count;
                picker.querySelector('[data-picker-empty]').hidden = count > 0;
                const badge = form.querySelector('[data-selection-count="' + picker.dataset.contentPicker + '"]');
                if (badge) badge.textContent = count;
                const term = normalize(search.value.trim());
                let visible = 0;
                [...available.children].forEach(item => { item.hidden = item.dataset.unavailable === '1' || !normalize(item.dataset.search).includes(term); if (!item.hidden) visible++; });
                picker.querySelector('[data-picker-no-results]').hidden = visible > 0;
            };
            search.addEventListener('input', refresh);
            picker.addEventListener('change', event => {
                const input = event.target;
                if (!input.matches('input[type="checkbox"]')) return;
                const item = input.closest('[data-picker-item]');
                if (input.checked && selected.children.length >= 24) {
                    input.checked = false;
                    picker.querySelector('[data-picker-status]').textContent = 'Bu bölümde en fazla 24 kayıt seçebilirsiniz.';
                } else {
                    (input.checked ? selected : available).append(item);
                    item.hidden = false;
                    picker.querySelector('[data-picker-status]').textContent = input.checked ? 'İçerik sayfa seçkisine eklendi.' : 'İçerik seçkiden kaldırıldı.';
                }
                refresh();
            });
            picker.addEventListener('click', event => {
                const button = event.target.closest('[data-picker-remove],[data-picker-move]');
                if (!button) return;
                const item = button.closest('[data-picker-item]');
                if (button.hasAttribute('data-picker-remove')) {
                    const input = item.querySelector('input');
                    input.checked = false;
                    input.dispatchEvent(new Event('change', {bubbles: true}));
                    input.dispatchEvent(new Event('input', {bubbles: true}));
                } else {
                    const sibling = button.dataset.pickerMove === 'up' ? item.previousElementSibling : item.nextElementSibling;
                    if (sibling) {
                        if (button.dataset.pickerMove === 'up') selected.insertBefore(item, sibling); else selected.insertBefore(sibling, item);
                        item.querySelector('input').dispatchEvent(new Event('input', {bubbles: true}));
                        picker.querySelector('[data-picker-status]').textContent = 'Seçim sırası güncellendi.';
                    }
                }
                refresh();
            });
            refresh();
        });

        form.addEventListener('click', event => {
            const add = event.target.closest('[data-add-content-row]');
            if (add) {
                const row = [...form.querySelectorAll('[data-content-row]')].find(item => item.hidden);
                if (row) { row.hidden = false; row.open = true; row.querySelector('input').focus(); }
                else add.textContent = 'En fazla 12 içerik bölümü eklenebilir';
                return;
            }
            const clear = event.target.closest('[data-remove-content-row]');
            if (clear) {
                const row = clear.closest('[data-content-row]');
                row.querySelectorAll('input,textarea').forEach(field => { field.value = ''; });
                row.open = false; row.hidden = true;
                row.querySelector('input').dispatchEvent(new Event('input', {bubbles: true}));
                return;
            }
            const order = event.target.closest('[data-section-move]');
            if (order) {
                const row = order.closest('[data-order-item]');
                const other = order.dataset.sectionMove === 'up' ? row.previousElementSibling : row.nextElementSibling;
                if (other) {
                    if (order.dataset.sectionMove === 'up') row.parentElement.insertBefore(row, other); else row.parentElement.insertBefore(other, row);
                    row.querySelector('input').dispatchEvent(new Event('input', {bubbles: true}));
                }
                return;
            }
            const button = event.target.closest('[data-move-row]');
            if (!button) return;
            const row = button.closest('[data-content-row]');
            const rows = [...form.querySelectorAll('[data-content-row]')].filter(item => !item.hidden);
            const index = rows.indexOf(row), other = rows[index + (button.dataset.moveRow === 'up' ? -1 : 1)];
            if (!other) return;
            const a = [...row.querySelectorAll('input,textarea')], b = [...other.querySelectorAll('input,textarea')];
            a.forEach((field, n) => { const value = field.value; field.value = b[n].value; b[n].value = value; });
            other.open = true; button.blur(); other.querySelector('input')?.focus();
            rows.forEach((item, n) => { const title = item.querySelector('input')?.value || ''; item.querySelector('summary').textContent = 'Bölüm ' + (n + 1) + (title ? ' — ' + title : ''); });
            other.querySelector('input')?.dispatchEvent(new Event('input', {bubbles: true}));
        });
    });
})();
