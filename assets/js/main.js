const nav = document.querySelector('.portfolio-nav');

window.addEventListener('scroll', () => {
  nav?.classList.toggle('shadow-sm', window.scrollY > 12);
});

document.querySelectorAll('a[href^="#"], a[href^="/#"]').forEach((link) => {
  link.addEventListener('click', (event) => {
    const href = link.getAttribute('href');
    if (href.startsWith('/#') && window.location.pathname !== '/') return;
    const target = document.querySelector(href.replace(/^\//, ''));
    if (!target) return;

    event.preventDefault();
    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.16 });

document.querySelectorAll('.reveal').forEach((element) => {
  revealObserver.observe(element);
});

document.querySelectorAll('[data-tilt-card]').forEach((card) => {
  card.addEventListener('mousemove', (event) => {
    const rect = card.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width - 0.5;
    const y = (event.clientY - rect.top) / rect.height - 0.5;

    card.style.setProperty('--tilt-x', `${y * -6}deg`);
    card.style.setProperty('--tilt-y', `${x * 8}deg`);
  });

  card.addEventListener('mouseleave', () => {
    card.style.setProperty('--tilt-x', '0deg');
    card.style.setProperty('--tilt-y', '0deg');
  });
});

// Filtro de proyectos por categoría, sin recargar.
document.querySelectorAll('[data-filtro]').forEach((chip) => {
  chip.addEventListener('click', () => {
    const filtro = chip.dataset.filtro;
    document.querySelectorAll('[data-filtro]').forEach((other) => {
      const activo = other === chip;
      other.classList.toggle('is-active', activo);
      other.setAttribute('aria-pressed', activo ? 'true' : 'false');
    });
    document.querySelectorAll('[data-project-grid] .project-card').forEach((card) => {
      card.hidden = filtro !== 'todos' && card.dataset.categoria !== filtro;
    });
  });
});

document.querySelectorAll('[data-project-viewer]').forEach((viewer) => {
  const track = viewer.querySelector('[data-project-track]');
  const prev = viewer.querySelector('[data-viewer-prev]');
  const next = viewer.querySelector('[data-viewer-next]');

  const move = (direction) => {
    const card = track?.querySelector('.project-card');
    if (!track || !card) return;

    const gap = Number.parseFloat(getComputedStyle(track).columnGap) || 16;
    track.scrollBy({ left: direction * (card.clientWidth + gap), behavior: 'smooth' });
  };

  prev?.addEventListener('click', () => move(-1));
  next?.addEventListener('click', () => move(1));

  track?.addEventListener('keydown', (event) => {
    if (event.key === 'ArrowLeft') {
      event.preventDefault();
      move(-1);
    }

    if (event.key === 'ArrowRight') {
      event.preventDefault();
      move(1);
    }
  });
});
