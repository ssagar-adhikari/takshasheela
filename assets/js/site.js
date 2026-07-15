const toggle = document.querySelector('[data-nav-toggle]');
const navigation = document.querySelector('[data-nav]');

if (toggle && navigation) {
  toggle.addEventListener('click', () => {
    const isOpen = toggle.getAttribute('aria-expanded') === 'true';
    toggle.setAttribute('aria-expanded', String(!isOpen));
    navigation.classList.toggle('is-open', !isOpen);
    document.body.classList.toggle('nav-open', !isOpen);
  });

  navigation.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      toggle.setAttribute('aria-expanded', 'false');
      navigation.classList.remove('is-open');
      document.body.classList.remove('nav-open');
    });
  });
}

const navDetails = document.querySelectorAll('.site-nav > ul > .nav-group > details');
navDetails.forEach((details) => {
  details.addEventListener('toggle', () => {
    if (details.open) {
      navDetails.forEach((other) => {
        if (other !== details) other.removeAttribute('open');
      });
    }
  });
});

document.querySelectorAll('.nav-panel--wellness').forEach((panel) => {
  const subgroups = panel.querySelectorAll('.nav-subgroup');
  subgroups.forEach((subgroup) => {
    subgroup.addEventListener('toggle', () => {
      if (subgroup.open) {
        subgroups.forEach((other) => {
          if (other !== subgroup) other.removeAttribute('open');
        });
      }
    });
  });
});

document.querySelectorAll('.nav-submenu').forEach((submenu) => {
  const itemGroups = submenu.querySelectorAll(':scope > .nav-item-group');
  itemGroups.forEach((itemGroup) => {
    itemGroup.addEventListener('toggle', () => {
      if (itemGroup.open) {
        itemGroups.forEach((other) => {
          if (other !== itemGroup) other.removeAttribute('open');
        });
      }
    });
  });
});

const desktopNavigation = window.matchMedia('(min-width: 861px)');
document.querySelectorAll('.site-nav details').forEach((details) => {
  details.addEventListener('mouseenter', () => {
    if (desktopNavigation.matches) details.open = true;
  });
  details.addEventListener('mouseleave', () => {
    if (desktopNavigation.matches) details.open = false;
  });
});

document.addEventListener('click', (event) => {
  if (!event.target.closest('.nav-group')) {
    navDetails.forEach((details) => details.removeAttribute('open'));
  }
});

const header = document.querySelector('[data-header]');
const updateHeader = () => header?.classList.toggle('is-scrolled', window.scrollY > 24);
updateHeader();
window.addEventListener('scroll', updateHeader, { passive: true });

const heroVideo = document.querySelector('[data-hero-video]');
if (heroVideo && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  heroVideo.pause();
  heroVideo.removeAttribute('autoplay');
}

document.querySelectorAll('[data-lightbox]').forEach((button) => {
  button.addEventListener('click', () => {
    const dialog = document.querySelector('#gallery-dialog');
    const image = dialog?.querySelector('img');
    if (dialog && image) {
      image.src = button.dataset.lightbox;
      image.alt = button.querySelector('img')?.alt || '';
      dialog.showModal();
    }
  });
});

document.querySelector('[data-dialog-close]')?.addEventListener('click', () => {
  document.querySelector('#gallery-dialog')?.close();
});
