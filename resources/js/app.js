import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* ===========================
   Gallery (thumbnails + arrows)
   =========================== */
(function () {
  const main = document.getElementById('gallery-main');
  if (!main) return;

  // parent container (το wrapper της εικόνας)
  const container = main.parentElement;
  if (container && getComputedStyle(container).position === 'static') {
    // βεβαιώνουμε ότι είναι relative για να «κάθονται» σωστά τα absolute βελάκια
    container.style.position = 'relative';
  }

  const thumbsWrap = document.getElementById('gallery-thumbs');
  let thumbs = [];

  // Μαζεύουμε thumbs αν υπάρχουν, αλλιώς φτιάχνουμε ένα "ghost" για single image cases
  if (thumbsWrap) {
    thumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
  } else if (main.src) {
    const ghost = document.createElement('button');
    ghost.dataset.index = '0';
    ghost.dataset.src = main.src;
    thumbs = [ghost];
  }

  // Βρίσκουμε ή δημιουργούμε τα arrows
  let prevBtn = document.getElementById('gallery-prev');
  let nextBtn = document.getElementById('gallery-next');

  const arrowClasses =
    'absolute z-20 w-10 h-10 md:w-12 md:h-12 grid place-items-center rounded-full ' +
    'bg-gray-200/90 hover:bg-gray-300 text-3xl md:text-4xl leading-none text-gray-800 ' +
    'shadow ring-1 ring-black/5';

  if (!prevBtn && container) {
    prevBtn = document.createElement('button');
    prevBtn.id = 'gallery-prev';
    prevBtn.type = 'button';
    prevBtn.setAttribute('aria-label', 'Previous');
    prevBtn.className = `${arrowClasses} left-2 top-1/2 -translate-y-1/2`;
    prevBtn.textContent = '‹';
    container.appendChild(prevBtn);
  }
  if (!nextBtn && container) {
    nextBtn = document.createElement('button');
    nextBtn.id = 'gallery-next';
    nextBtn.type = 'button';
    nextBtn.setAttribute('aria-label', 'Next');
    nextBtn.className = `${arrowClasses} right-2 top-1/2 -translate-y-1/2`;
    nextBtn.textContent = '›';
    container.appendChild(nextBtn);
  }

  let idx = parseInt(main.getAttribute('data-index') || '0', 10) || 0;

  function setActive(i) {
    if (!thumbs.length) return;
    idx = (i + thumbs.length) % thumbs.length;
    const src = thumbs[idx].dataset.src || main.src;
    main.src = src;
    main.setAttribute('data-index', String(idx));

    // ενημέρωση active thumb (αν υπάρχει strip)
    if (thumbsWrap) {
      const realThumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
      realThumbs.forEach((b, j) =>
        j === idx ? b.setAttribute('data-active', 'true') : b.removeAttribute('data-active')
      );
    }
  }

  // Αρχικοποίηση
  setActive(idx);

  // Arrows — δουλεύουν και σε single image (no-op ουσιαστικά)
  prevBtn?.addEventListener('click', () => setActive(idx - 1));
  nextBtn?.addEventListener('click', () => setActive(idx + 1));

  // Click στα thumbs
  if (thumbsWrap) {
    const realThumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
    realThumbs.forEach((b) =>
      b.addEventListener('click', () => setActive(parseInt(b.dataset.index, 10)))
    );
  }

  // Keyboard navigation
  window.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') setActive(idx - 1);
    if (e.key === 'ArrowRight') setActive(idx + 1);
  });

  // Touch swipe (στο container για καλύτερο UX)
  let startX = null;
  const touchTarget = container || main;
  touchTarget.addEventListener(
    'touchstart',
    (e) => {
      startX = e.touches[0].clientX;
    },
    { passive: true }
  );
  touchTarget.addEventListener(
    'touchend',
    (e) => {
      if (startX === null) return;
      const dx = e.changedTouches[0].clientX - startX;
      if (Math.abs(dx) > 40) {
        if (dx < 0) setActive(idx + 1);
        else setActive(idx - 1);
      }
      startX = null;
    },
    { passive: true }
  );
})();

/* ===========================
   Lightbox (zoom, pan, arrows)
   =========================== */
(function () {
  const main = document.getElementById('gallery-main');
  const thumbsWrap = document.getElementById('gallery-thumbs');
  const lb = document.getElementById('lightbox');
  if (!main || !lb) return;

  // if there is no thumbs strip (single image), create a virtual one with the main image
  let thumbs = [];
  if (thumbsWrap) {
    thumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
  } else if (main.src) {
    const ghost = document.createElement('button');
    ghost.dataset.index = '0';
    ghost.dataset.src = main.src;
    thumbs = [ghost];
  }

  const lbImg = document.getElementById('lb-img');
  const lbStage = document.getElementById('lb-stage');
  const lbPrev = document.getElementById('lb-prev');
  const lbNext = document.getElementById('lb-next');
  const lbClose = document.getElementById('lb-close');
  const lbCounter = document.getElementById('lb-counter');
  const lbBackdrop = document.getElementById('lb-backdrop');

  let idx = parseInt(main.getAttribute('data-index') || '0', 10);

  function setIdx(i) {
    if (!thumbs.length) return;
    idx = (i + thumbs.length) % thumbs.length;
    const src = thumbs[idx].dataset.src || main.src;
    lbImg.src = src;
    lbImg.style.transform = 'translate(0px,0px) scale(1)';
    lbImg.classList.remove('cursor-zoom-out');
    lbImg.classList.add('cursor-zoom-in');
    if (lbCounter) lbCounter.textContent = `${idx + 1} / ${thumbs.length}`;
    main.src = src;
    main.setAttribute('data-index', String(idx));
    if (thumbsWrap) {
      const realThumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
      realThumbs.forEach((b, j) =>
        j === idx ? b.setAttribute('data-active', 'true') : b.removeAttribute('data-active')
      );
    }
  }

  function openLb() {
    lb.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setIdx(idx);
  }
  function closeLb() {
    lb.classList.add('hidden');
    document.body.style.overflow = '';
    resetPanZoom();
  }

  // Openers
  main.addEventListener('click', openLb);
  if (thumbsWrap) {
    const realThumbs = Array.from(thumbsWrap.querySelectorAll('button[data-index]'));
    realThumbs.forEach((b) => b.addEventListener('dblclick', openLb));
  }

  // Nav
  lbPrev?.addEventListener('click', () => setIdx(idx - 1));
  lbNext?.addEventListener('click', () => setIdx(idx + 1));
  lbClose?.addEventListener('click', closeLb);
  lbBackdrop?.addEventListener('click', closeLb);
  lb.addEventListener('click', (e) => {
    if (e.target === lb) closeLb();
  });

  // Keyboard
  window.addEventListener('keydown', (e) => {
    if (lb.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowLeft') setIdx(idx - 1);
    if (e.key === 'ArrowRight') setIdx(idx + 1);
  });

  // Swipe on stage
  let sx = null,
    sy = null;
  lbStage.addEventListener(
    'touchstart',
    (e) => {
      sx = e.touches[0].clientX;
      sy = e.touches[0].clientY;
    },
    { passive: true }
  );
  lbStage.addEventListener(
    'touchend',
    (e) => {
      if (sx === null) return;
      const dx = e.changedTouches[0].clientX - sx;
      const dy = e.changedTouches[0].clientY - sy;
      if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
        if (dx < 0) setIdx(idx + 1);
        else setIdx(idx - 1);
      }
      sx = sy = null;
    },
    { passive: true }
  );

  // Zoom & pan
  let scale = 1,
    tx = 0,
    ty = 0,
    dragging = false,
    px = 0,
    py = 0;
  function resetPanZoom() {
    scale = 1;
    tx = 0;
    ty = 0;
    dragging = false;
    lbImg.style.transform = 'translate(0px,0px) scale(1)';
  }
  function apply() {
    lbImg.style.transform = `translate(${tx}px,${ty}px) scale(${scale})`;
  }

  lbImg.addEventListener('click', () => {
    if (scale === 1) {
      scale = 2;
      lbImg.classList.remove('cursor-zoom-in');
      lbImg.classList.add('cursor-zoom-out');
    } else {
      resetPanZoom();
      lbImg.classList.remove('cursor-zoom-out');
      lbImg.classList.add('cursor-zoom-in');
    }
    apply();
  });
  lbImg.addEventListener(
    'wheel',
    (e) => {
      e.preventDefault();
      const delta = e.deltaY > 0 ? -0.1 : 0.1;
      scale = Math.min(4, Math.max(1, scale + delta));
      lbImg.classList.toggle('cursor-zoom-out', scale > 1);
      lbImg.classList.toggle('cursor-zoom-in', scale === 1);
      apply();
    },
    { passive: false }
  );
  lbImg.addEventListener('mousedown', (e) => {
    if (scale > 1) {
      dragging = true;
      px = e.clientX - tx;
      py = e.clientY - ty;
      lbImg.style.cursor = 'grabbing';
    }
  });
  window.addEventListener('mousemove', (e) => {
    if (dragging) {
      tx = e.clientX - px;
      ty = e.clientY - py;
      apply();
    }
  });
  window.addEventListener('mouseup', () => {
    dragging = false;
    lbImg.style.cursor = scale > 1 ? 'zoom-out' : 'zoom-in';
  });
})();

/* ==========================================
   Photo uploader (create/edit) — no file limit
   ========================================== */
function initPhotoUploader(inputSelector, previewSelector, max = Infinity) {
  const input = document.querySelector(inputSelector);
  const previews = document.querySelector(previewSelector);
  const statusEl = document.getElementById('photos-status');
  if (!input || !previews) return;

  let files = [];

  function updateStatus() {
    if (!statusEl) return;
    if (!files.length) statusEl.textContent = 'No files selected.';
    else statusEl.textContent = files.length === 1 ? '1 photo selected' : `${files.length} photos selected`;
  }

  function syncInputFiles() {
    const dt = new DataTransfer();
    files.forEach((f) => dt.items.add(f));
    input.files = dt.files;
  }

  function renderPreviews() {
    previews.innerHTML = '';
    files.forEach((file, idx) => {
      const url = URL.createObjectURL(file);
      const wrap = document.createElement('div');
      wrap.className = 'relative';
      const img = document.createElement('img');
      img.src = url;
      img.alt = file.name;
      img.className = 'h-24 w-24 object-cover rounded border';
      wrap.appendChild(img);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = '×';
      btn.title = 'Remove';
      btn.className =
        'absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs flex items-center justify-center';
      btn.addEventListener('click', () => {
        files.splice(idx, 1);
        syncInputFiles();
        renderPreviews();
        updateStatus();
      });
      wrap.appendChild(btn);
      previews.appendChild(wrap);
    });
  }

  // Allow selecting same filenames repeatedly
  input.addEventListener('click', () => {
    input.value = '';
  });

  input.addEventListener('change', (e) => {
    const selected = Array.from(e.target.files);
    const sig = (f) => `${f.name}|${f.size}|${f.lastModified}`;
    const existing = new Set(files.map(sig));
    for (const f of selected) {
      if (files.length >= max) break;
      if (!existing.has(sig(f))) files.push(f);
    }
    syncInputFiles();
    renderPreviews();
    updateStatus();
  });

  updateStatus();
}

/* ==========================================
   Select-all & counter for delete_images[] (edit)
   ========================================== */
function initDeleteSelectionUI(wrapperSelector, selectAllSelector, counterSelector) {
  const wrap = document.querySelector(wrapperSelector);
  if (!wrap) return;
  const selectAll = document.querySelector(selectAllSelector);
  const counterEl = document.querySelector(counterSelector);

  const boxes = () =>
    Array.from(wrap.querySelectorAll('input[type=checkbox][name="delete_images[]"]'));

  function updateCounter() {
    const n = boxes().filter((b) => b.checked).length;
    if (counterEl) counterEl.textContent = `${n} επιλεγμένες`;
  }

  wrap.addEventListener('change', (e) => {
    if (e.target && e.target.matches('input[type=checkbox][name="delete_images[]"]')) {
      updateCounter();
    }
  });

  if (selectAll) {
    selectAll.addEventListener('change', () => {
      const all = boxes();
      all.forEach((b) => (b.checked = selectAll.checked));
      updateCounter();
    });
  }

  updateCounter();
}

/* ===========================
   Share button
   =========================== */
function initShareButton(selector) {
  const btn = document.querySelector(selector);
  if (!btn) return;
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    const data = { title: document.title, text: 'Check out this listing', url: window.location.href };
    if (navigator.share) {
      try {
        await navigator.share(data);
      } catch {}
    } else {
      try {
        await navigator.clipboard.writeText(data.url);
        const old = btn.textContent;
        btn.textContent = '✅ Link copied';
        setTimeout(() => (btn.textContent = old), 1500);
      } catch {
        alert(data.url);
      }
    }
  });
}

/* ===========================
   Phone modal (simple)
   =========================== */
function initPhoneModal() {
  const btn = document.getElementById('reveal-phone-btn');
  const modal = document.getElementById('phone-modal');
  if (!btn || !modal) return;

  const backdrop = document.getElementById('phone-backdrop');
  const closeBtn = document.getElementById('phone-close');
  const numberEl = document.getElementById('phone-number');
  const callLink = document.getElementById('phone-call');

  const open = () => {
    const display = (btn.dataset.phoneDisplay || '').trim();
    const href = (btn.dataset.phoneHref || '#').trim();
    if (numberEl) numberEl.textContent = display || '—';
    if (callLink) callLink.href = href;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };
  const close = () => {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  };

  btn.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  backdrop?.addEventListener('click', close);
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
  });
}

/* ===========================
   Email modal (auth users)
   =========================== */
function initEmailModal() {
  const openBtn = document.getElementById('open-email-btn');
  const modal = document.getElementById('email-modal');
  if (!openBtn || !modal) return;
  const backdrop = document.getElementById('email-backdrop');
  const closeBtn = document.getElementById('email-close');
  const open = () => {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };
  const close = () => {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  };
  openBtn.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  backdrop?.addEventListener('click', close);
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
  });
}

/* ===========================
   Login-required modal (guests)
   =========================== */
function initLoginRequiredModal() {
  const openBtn = document.getElementById('open-email-login');
  const modal = document.getElementById('login-required-modal');
  if (!openBtn || !modal) return;
  const backdrop = document.getElementById('login-required-backdrop');
  const closeBtn = document.getElementById('login-required-close');
  const open = () => {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  };
  const close = () => {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
  };
  openBtn.addEventListener('click', open);
  closeBtn?.addEventListener('click', close);
  backdrop?.addEventListener('click', close);
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
  });
}

/* ===========================
   Dashboard: Delete modal
   =========================== */
function initDashboardDeleteModal(){
  const modal    = document.getElementById('delete-modal');
  if (!modal) return; // δεν είμαστε στο dashboard

  const backdrop = document.getElementById('delete-backdrop');
  const form     = document.getElementById('del-form');
  const nameEl   = document.getElementById('del-name');
  const closeBtn = document.getElementById('del-close');
  const cancel   = document.getElementById('del-cancel');
  const confirm  = document.getElementById('del-confirm');
  const triggers = Array.from(document.querySelectorAll('.btn-open-delete'));

  let targetUrl = null;

  function open(url, title){
    targetUrl   = url;
    form.action = url;
    if (nameEl) nameEl.textContent = title || '';
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }
  function close(){
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    targetUrl = null;
  }

  triggers.forEach(b=>{
    b.addEventListener('click', ()=> open(b.dataset.url, b.dataset.title));
  });

  confirm?.addEventListener('click', ()=> { if (targetUrl) form.submit(); });
  cancel?.addEventListener('click', close);
  closeBtn?.addEventListener('click', close);
  backdrop?.addEventListener('click', close);
  window.addEventListener('keydown', (e)=>{ if(e.key==='Escape' && !modal.classList.contains('hidden')) close(); });
}


/* ===========================
   Boot
   =========================== */
document.addEventListener('DOMContentLoaded', () => {
  // Unlimited uploader
  initPhotoUploader('#photos', '#previews'); // no limit

  // Select-all + counter for delete checkboxes (edit page only; safe if not present)
  initDeleteSelectionUI('#existing-images', '#del-select-all', '#del-counter');

  // Share
  initShareButton('#share-btn');

  // Lightbox opener button (if present)
  const openBtn = document.getElementById('gallery-open');
  const mainImg = document.getElementById('gallery-main');
  if (openBtn && mainImg) openBtn.addEventListener('click', () => mainImg.click());

  // Modals
  initPhoneModal();
  initEmailModal();
  initLoginRequiredModal();

  // Delete ad
  initDashboardDeleteModal();
});
