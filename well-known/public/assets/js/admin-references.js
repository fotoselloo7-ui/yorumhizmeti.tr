document.querySelectorAll('[data-reference-editor]').forEach(function(form) {
  const group = form.querySelector('[data-ref-group]');
  const service = form.querySelector('[data-ref-service]');
  const secondGroup = form.querySelector('[data-ref-second-group]');
  const secondService = form.querySelector('[data-ref-second-service]');
  const media = form.querySelector('[data-ref-media]');
  const videoField = form.querySelector('[data-ref-video-field]');
  const videoInput = form.querySelector('[data-ref-video-input]');
  const hostedVideoField = form.querySelector('[data-ref-hosted-video-field]');
  const hostedVideoInput = form.querySelector('[data-ref-hosted-video]');
  const url = form.querySelector('[data-ref-url]');
  const hint = form.querySelector('[data-ref-url-hint]');
  if (!group || !service || !media || !url) return;
  function sync() {
    let selectedVisible = false;
    Array.from(service.options).forEach(function(option) {
      const visible = option.dataset.parent === group.value;
      option.hidden = !visible;
      option.disabled = !visible;
      if (visible && option.selected) selectedVisible = true;
    });
    if (!selectedVisible) {
      const first = Array.from(service.options).find(o => !o.disabled);
      if (first) service.value = first.value;
    }
    if (secondGroup && secondService) {
      const enabled = secondGroup.value !== '';
      secondService.disabled = !enabled;
      secondService.required = enabled;
      let validSelection = false;
      Array.from(secondService.options).forEach(function(option) {
        if (!option.value) { option.hidden = enabled; return; }
        const visible = enabled && option.dataset.parent === secondGroup.value;
        option.hidden = !visible;
        option.disabled = !visible;
        if (visible && option.selected) validSelection = true;
      });
      if (!enabled) {
        secondService.value = '';
      } else if (!validSelection) {
        const first = Array.from(secondService.options).find(option => !option.disabled && option.value);
        if (first) secondService.value = first.value;
      }
    }
    const instagram = media.value.startsWith('instagram_');
    const youtube = media.value === 'youtube_video';
    if (videoField) videoField.style.display = instagram ? '' : 'none';
    if (videoInput) videoInput.disabled = !instagram;
    if (hostedVideoField) hostedVideoField.style.display = instagram ? '' : 'none';
    if (hostedVideoInput) hostedVideoInput.disabled = !instagram;
    url.placeholder = youtube ? 'https://www.youtube.com/watch?v=... veya https://youtu.be/...' :
      instagram ? (media.value === 'instagram_reel' ? 'https://www.instagram.com/reel/ABC123/' : 'https://www.instagram.com/p/ABC123/') :
      'https://musteri-sitesi.com';
    url.required = instagram || youtube || media.value === 'website';
    if (hint) hint.textContent = youtube ?
      'YouTube videosu veya Shorts bağlantısı girin. Kapağa basılınca video sitemizde açılır.' :
      instagram ? 'Instagram linki tek başına eklenirse Videoyu İzle, Instagram’da açar. Aynı referansa ayrıca harici video bağlantısı eklenirse kendi sitemizde oynatılır.' :
      (media.value === 'website' ? 'Müşteri sitesine direkt yönlendirilir.' : 'İsteğe bağlı görsel bağlantısı.');
  }
  group.addEventListener('change', sync);
  if (secondGroup) secondGroup.addEventListener('change', sync);
  media.addEventListener('change', sync);
  sync();
});
