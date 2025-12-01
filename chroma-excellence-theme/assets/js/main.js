/**
 * Main JavaScript
 * Data-attribute based modular approach
 *
 * @package Chroma_Excellence
 */

document.addEventListener('DOMContentLoaded', function () {
  const safeParseJSON = (value, fallback) => {
    try {
      return JSON.parse(value);
    } catch (e) {
      console.warn('Failed to parse JSON payload', e);
      return fallback;
    }
  };

  /**
   * Mobile Nav Toggle
   */
  const mobileNavToggles = document.querySelectorAll('[data-mobile-nav-toggle]');
  const mobileNav = document.querySelector('[data-mobile-nav]');

  mobileNavToggles.forEach((toggle) => {
    toggle.addEventListener('click', () => {
      mobileNav.classList.toggle('translate-x-full');
      // Toggle body scroll
      if (!mobileNav.classList.contains('translate-x-full')) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    });
  });

  // Close menu on link click
  if (mobileNav) {
    mobileNav.querySelectorAll('a[href^="#"]').forEach((link) => {
      link.addEventListener('click', () => {
        mobileNav.classList.add('translate-x-full');
      });
    });
  }

  /**
   * Accordions
   */
  const accordions = document.querySelectorAll('[data-accordion]');

  accordions.forEach((accordion) => {
    const triggers = accordion.querySelectorAll('[data-accordion-trigger]');

    triggers.forEach((trigger) => {
      trigger.addEventListener('click', () => {
        const targetId = trigger.getAttribute('aria-controls');
        const content = document.getElementById(targetId);

        if (!content) return;

        const isOpen = !content.classList.contains('hidden');

        // Close all in this accordion
        accordion.querySelectorAll('[data-accordion-content]').forEach((c) => {
          c.classList.add('hidden');
        });

        // Toggle current
        if (!isOpen) {
          content.classList.remove('hidden');
        }
      });
    });
  });

  /**
   * Programs wizard
   */
  const wizard = document.querySelector('[data-program-wizard]');
  if (wizard) {
    const options = safeParseJSON(wizard.getAttribute('data-options') || '[]', []);
    const optionButtons = wizard.querySelectorAll('[data-program-wizard-option]');
    const result = wizard.querySelector('[data-program-wizard-result]');
    const title = wizard.querySelector('[data-program-wizard-title]');
    const desc = wizard.querySelector('[data-program-wizard-description]');
    const learnLink = wizard.querySelector('[data-program-wizard-link]');
    const resetBtn = wizard.querySelector('[data-program-wizard-reset]');

    optionButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        const key = btn.getAttribute('data-program-wizard-option');
        const selected = options.find((o) => o.key === key);
        if (!selected || !result) return;

        wizard.querySelector('[data-program-wizard-options]')?.classList.add('hidden');
        result.classList.remove('hidden');
        if (title) title.textContent = selected.label;
        if (desc) desc.textContent = selected.description;
        if (learnLink && selected.link) learnLink.setAttribute('href', selected.link);
      });
    });

    resetBtn?.addEventListener('click', () => {
      wizard.querySelector('[data-program-wizard-options]')?.classList.remove('hidden');
      result?.classList.add('hidden');
    });
  }

  /**
   * Curriculum radar chart
   */
  const curriculumConfigEl = document.querySelector('[data-curriculum-config]');
  const curriculumChartEl = document.querySelector('[data-curriculum-chart]');
  const curriculumButtons = document.querySelectorAll('[data-curriculum-button]');

  if (curriculumConfigEl && curriculumChartEl) {
    const config = safeParseJSON(curriculumConfigEl.textContent || '{}', {});
    const profiles = config.profiles || [];
    const labels = config.labels || [];
    const defaultProfile = profiles[0];
    let chartInstance = null;

    const setActiveProfile = (key) => {
      const profile = profiles.find((p) => p.key === key) || defaultProfile;
      if (!profile) return;

      curriculumButtons.forEach((btn) => {
        const isActive = btn.getAttribute('data-curriculum-button') === profile.key;
        if (isActive) {
          btn.classList.add('bg-chroma-blue', 'text-white', 'shadow-soft');
          btn.classList.remove('text-brand-ink/70', 'bg-white');
        } else {
          btn.classList.remove('bg-chroma-blue', 'text-white', 'shadow-soft');
          btn.classList.add('text-brand-ink/70', 'bg-white');
        }
      });

      const title = document.querySelector('[data-curriculum-title]');
      const description = document.querySelector('[data-curriculum-description]');
      if (title) title.textContent = profile.title;
      if (description) description.textContent = profile.description;

      if (window.Chart && chartInstance) {
        chartInstance.data.datasets[0].data = profile.data;
        chartInstance.data.datasets[0].borderColor = profile.color;
        chartInstance.data.datasets[0].backgroundColor = `${profile.color}33`;
        chartInstance.data.datasets[0].pointBorderColor = profile.color;
        chartInstance.update();
      }
    };

    if (window.Chart) {
      // Use double requestAnimationFrame to prevent forced reflow during chart initialization
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          chartInstance = new Chart(curriculumChartEl.getContext('2d'), {
            type: 'radar',
            data: {
              labels,
              datasets: [
                {
                  label: 'Focus',
                  data: (defaultProfile && defaultProfile.data) || [],
                  borderColor: defaultProfile?.color || '#4A6C7C',
                  backgroundColor: `${defaultProfile?.color || '#4A6C7C'}33`,
                  borderWidth: 2,
                  pointBackgroundColor: '#ffffff',
                  pointBorderColor: defaultProfile?.color || '#4A6C7C',
                  pointRadius: 4,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: { legend: { display: false } },
              scales: {
                r: {
                  angleLines: { color: '#e5e7eb' },
                  grid: { color: '#e5e7eb' },
                  suggestedMin: 0,
                  suggestedMax: 100,
                  ticks: { display: false },
                  pointLabels: {
                    font: { family: 'Outfit, system-ui, sans-serif', size: 12 },
                    color: '#263238',
                  },
                },
              },
            },
          });
        });
      });
    }

    curriculumButtons.forEach((btn) => {
      btn.addEventListener('click', () => {
        setActiveProfile(btn.getAttribute('data-curriculum-button'));
      });
    });

    if (defaultProfile) {
      setActiveProfile(defaultProfile.key);
    }
  }

  /**
   * Schedule tabs
   */
  const schedule = document.querySelector('[data-schedule]');
  if (schedule) {
    const panels = schedule.querySelectorAll('[data-schedule-panel]');
    const tabs = schedule.querySelectorAll('[data-schedule-tab]');
    const defaultKey = tabs[0]?.getAttribute('data-schedule-tab');

    const activate = (key) => {
      tabs.forEach((btn) => {
        const isActive = btn.getAttribute('data-schedule-tab') === key;
        btn.classList.toggle('bg-chroma-blue', isActive);
        btn.classList.toggle('text-white', isActive);
        btn.classList.toggle('shadow-soft', isActive);
        btn.classList.toggle('text-brand-ink/60', !isActive);
        btn.style.color = isActive ? '#ffffff' : 'rgba(38, 50, 56, 0.6)';
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });

      panels.forEach((panel) => {
        const isMatch = panel.getAttribute('data-schedule-panel') === key;
        panel.classList.toggle('hidden', !isMatch);
        panel.classList.toggle('active', isMatch);
      });
    };

    tabs.forEach((btn) => {
      btn.addEventListener('click', () => {
        activate(btn.getAttribute('data-schedule-tab'));
      });
    });

    if (defaultKey) {
      activate(defaultKey);
    }
  }

  /**
   * Parent Reviews Carousel
   */
  const reviewsCarousel = document.querySelector('[data-reviews-carousel]');
  if (reviewsCarousel) {
    const track = reviewsCarousel.querySelector('[data-reviews-track]');
    const dots = reviewsCarousel.querySelectorAll('[data-review-dot]');
    const prevBtn = reviewsCarousel.querySelector('[data-review-prev]');
    const nextBtn = reviewsCarousel.querySelector('[data-review-next]');
    const slides = reviewsCarousel.querySelectorAll('[data-review-slide]');

    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoplayInterval = null;

    const goToSlide = (index) => {
      if (index < 0) index = totalSlides - 1;
      if (index >= totalSlides) index = 0;

      currentIndex = index;
      track.style.transform = `translateX(-${currentIndex * 100}%)`;

      // Update dots
      dots.forEach((dot, i) => {
        if (i === currentIndex) {
          dot.classList.remove('bg-chroma-blue/30', 'hover:bg-chroma-blue/50', 'w-3');
          dot.classList.add('bg-chroma-red', 'w-8');
        } else {
          dot.classList.remove('bg-chroma-red', 'w-8');
          dot.classList.add('bg-chroma-blue/30', 'hover:bg-chroma-blue/50', 'w-3');
        }
      });
    };

    const nextSlide = () => goToSlide(currentIndex + 1);
    const prevSlide = () => goToSlide(currentIndex - 1);

    // Dot navigation
    dots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        goToSlide(index);
        resetAutoplay();
      });
    });

    // Arrow navigation
    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        prevSlide();
        resetAutoplay();
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        nextSlide();
        resetAutoplay();
      });
    }

    // Autoplay
    const startAutoplay = () => {
      if (totalSlides > 1) {
        autoplayInterval = setInterval(nextSlide, 6000);
      }
    };

    const stopAutoplay = () => {
      if (autoplayInterval) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
      }
    };

    const resetAutoplay = () => {
      stopAutoplay();
      startAutoplay();
    };

    // Start autoplay on load
    startAutoplay();

    // Pause on hover
    reviewsCarousel.addEventListener('mouseenter', stopAutoplay);
    reviewsCarousel.addEventListener('mouseleave', startAutoplay);

    // Touch/swipe support
    let touchStartX = 0;
    let touchEndX = 0;

    track.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
    });

    track.addEventListener('touchend', (e) => {
      touchEndX = e.changedTouches[0].screenX;
      handleSwipe();
    });

    const handleSwipe = () => {
      const swipeThreshold = 50;
      if (touchStartX - touchEndX > swipeThreshold) {
        nextSlide();
        resetAutoplay();
      } else if (touchEndX - touchStartX > swipeThreshold) {
        prevSlide();
        resetAutoplay();
      }
    };
  }

  /**
   * Smooth Scrolling for Anchor Links
   */
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener('click', function (e) {
      const targetId = this.getAttribute('href');
      if (targetId === '#') return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        e.preventDefault();
        targetElement.scrollIntoView({ behavior: 'smooth' });
      }
    });
  });
});
