/* NetVera product detail v60: change sections under the tab bar without page jumps. */
(() => {
  "use strict";
  const init = () => {
    const copyText = async text => {
      try {
        if (navigator.clipboard?.writeText) {
          await navigator.clipboard.writeText(text);
          return true;
        }
      } catch (_) { /* Insecure contexts can still use the selection fallback. */ }
      const area = document.createElement('textarea');
      area.value = text;
      area.setAttribute('readonly', '');
      area.className = 'nv62-copy-buffer';
      document.body.appendChild(area);
      area.select();
      let copied = false;
      try { copied = document.execCommand('copy'); } catch (_) { copied = false; } finally { area.remove(); }
      return copied;
    };
    document.querySelectorAll('[data-nv62-copy],[data-copy-coupon]').forEach(button => {
      button.addEventListener('click', async () => {
        const value = button.hasAttribute('data-copy-coupon')
          ? button.dataset.copyCoupon
          : button.closest('.nv62-demo-row')?.querySelector('[data-nv62-value]')?.textContent;
        if (!value) return;
        const label = button.textContent;
        button.textContent = await copyText(value) ? 'Kopyalandı' : 'Tekrar dene';
        setTimeout(() => { button.textContent = label; }, 1500);
      });
    });
    document.querySelectorAll('[data-nv62-toggle]').forEach(button => {
      button.addEventListener('click', () => {
        const value = button.closest('.nv62-demo-row')?.querySelector('[data-nv62-secret]');
        if (!value) return;
        const hidden = value.classList.toggle('is-hidden');
        button.textContent = hidden ? 'Göster' : 'Gizle';
        button.setAttribute('aria-pressed', String(!hidden));
        button.setAttribute('aria-label', hidden ? 'Demo şifresini göster' : 'Demo şifresini gizle');
      });
    });
    document.querySelectorAll(".nv40-detail [data-nv60-tabs]").forEach(nav => {
      const root = nav.closest(".nv40-product-body");
      if (!root) return;
      const tabs = [...nav.querySelectorAll('[role="tab"][data-nv60-tab]')];
      const panels = tabs.map(t => root.querySelector('[role="tabpanel"][data-nv60-panel="' + t.dataset.nv60Tab + '"]'));
      if (!tabs.length || panels.some(p => !p)) return;
      const ids = new Set(tabs.map(t => t.dataset.nv60Tab));
      const article=root.querySelector("[data-nv60-article]");
      const toggle=root.querySelector("[data-nv60-description-toggle]");
      const refreshDescription = () => {
        // A hidden tab has no measurable height; defer until it is activated.
        if (!article || !toggle || article.closest('[hidden]')) return;
        const overflowing = article.scrollHeight > 575;
        toggle.hidden = !overflowing;
        article.classList.toggle("nv60-article-collapsed", overflowing && toggle.getAttribute("aria-expanded") !== "true");
      };
      if (article && toggle) {
        toggle.addEventListener("click",()=>{
          const expanded=toggle.getAttribute("aria-expanded")==="true";
          toggle.setAttribute("aria-expanded", expanded?"false":"true");
          article.classList.toggle("nv60-article-collapsed",expanded);
          toggle.textContent=expanded ? "Açıklamanın tamamını göster ↓" : "Açıklamayı daralt ↑";
        });
        window.addEventListener("resize", refreshDescription);
      }
      const activate = (id, options={}) => {
        const selected = ids.has(id) ? id : tabs[0].dataset.nv60Tab;
        tabs.forEach(t => {
          const active = t.dataset.nv60Tab === selected;
          t.setAttribute("aria-selected", active ? "true" : "false");
          t.tabIndex = active ? 0 : -1;
        });
        panels.forEach(p => {
          p.hidden = p.dataset.nv60Panel !== selected;
        });
        refreshDescription();
        if (options.focus) tabs.find(t => t.dataset.nv60Tab === selected)?.focus({preventScroll:true});
        if (options.updateUrl) {
          // replaceState changes the shareable section without triggering anchor scrolling.
          history.replaceState(history.state, "", location.pathname + location.search + "#" + selected);
        }
      };
      root.closest(".nv40-detail")?.querySelectorAll('[data-nv60-jump-tab]').forEach(link => {
        link.addEventListener("click", event => {
          const id=link.getAttribute("data-nv60-jump-tab");
          if(!ids.has(id))return;
          event.preventDefault();
          activate(id,{updateUrl:true});
          nav.scrollIntoView({block:"start",behavior:window.matchMedia("(prefers-reduced-motion: reduce)").matches?"auto":"smooth"});
        });
      });
      nav.addEventListener("click", e => {
        const tab=e.target.closest('[role="tab"]');
        if (!tab || !nav.contains(tab)) return;
        activate(tab.dataset.nv60Tab, {updateUrl:true});
      });
      nav.addEventListener("keydown", e => {
        if (!["ArrowLeft","ArrowRight","Home","End"].includes(e.key)) return;
        const current=tabs.indexOf(document.activeElement);
        if (current<0) return;
        e.preventDefault();
        const next=e.key==="Home" ? 0 : e.key==="End" ? tabs.length-1 :
          (current+(e.key==="ArrowRight"?1:-1)+tabs.length)%tabs.length;
        activate(tabs[next].dataset.nv60Tab,{focus:true,updateUrl:true});
      });
      const initial=decodeURIComponent(location.hash.slice(1));
      // Legacy compatibility: ?type=purchase opens the actual request tab, not a dead link.
      const purchaseIntent=new URLSearchParams(location.search).get('type')==='purchase';
      activate(ids.has(initial)?initial:(purchaseIntent && ids.has('teklif-al') ? 'teklif-al' : tabs[0].dataset.nv60Tab));
      window.addEventListener("hashchange", () => {
        const id=decodeURIComponent(location.hash.slice(1));
        if(ids.has(id)) activate(id);
      });
    });
  };
  if(document.readyState==="loading") document.addEventListener("DOMContentLoaded", init, {once:true});
  else init();
})();
