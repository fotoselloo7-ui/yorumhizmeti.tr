/* NetVera product detail v60: change sections under the tab bar without page jumps. */
(() => {
  "use strict";
  const init = () => {
    document.querySelectorAll(".nv40-detail [data-nv60-tabs]").forEach(nav => {
      const root = nav.closest(".nv40-product-body");
      if (!root) return;
      const tabs = [...nav.querySelectorAll('[role="tab"][data-nv60-tab]')];
      const panels = tabs.map(t => root.querySelector('[role="tabpanel"][data-nv60-panel="' + t.dataset.nv60Tab + '"]'));
      if (!tabs.length || panels.some(p => !p)) return;
      const ids = new Set(tabs.map(t => t.dataset.nv60Tab));
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
        if (options.focus) tabs.find(t => t.dataset.nv60Tab === selected)?.focus({preventScroll:true});
        if (options.updateUrl) {
          // replaceState changes the shareable section without triggering anchor scrolling.
          history.replaceState(history.state, "", location.pathname + location.search + "#" + selected);
        }
      };
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
      activate(ids.has(initial)?initial:tabs[0].dataset.nv60Tab);
      window.addEventListener("hashchange", () => {
        const id=decodeURIComponent(location.hash.slice(1));
        if(ids.has(id)) activate(id);
      });
      const article=root.querySelector("[data-nv60-article]");
      const toggle=root.querySelector("[data-nv60-description-toggle]");
      if (article && toggle) {
        const inner=article.scrollHeight;
        if (inner <= 575) {
          article.classList.remove("nv60-article-collapsed");
          toggle.hidden=true;
        } else {
          toggle.addEventListener("click",()=>{
            const expanded=toggle.getAttribute("aria-expanded")==="true";
            toggle.setAttribute("aria-expanded", expanded?"false":"true");
            article.classList.toggle("nv60-article-collapsed",expanded);
            toggle.textContent=expanded ? "Açıklamanın tamamını göster ↓" : "Açıklamayı daralt ↑";
          });
        }
      }
    });
  };
  if(document.readyState==="loading") document.addEventListener("DOMContentLoaded", init, {once:true});
  else init();
})();
