import './bootstrap'

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()

import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import 'swiper/css/effect-fade'

import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules'

document.addEventListener('DOMContentLoaded', () => {
  // HERO SWIPER
  const el = document.querySelector('.js-hero-swiper')
  if (el) {
    // destroy old instance (vite/hmr)
    if (el.swiper) el.swiper.destroy(true, true)

    const slideCount = el.querySelectorAll('.swiper-slide').length
    const canLoop = slideCount > 1

    const swiper = new Swiper(el, {
      modules: [Navigation, Pagination, Autoplay, EffectFade],

      loop: canLoop,
      effect: 'fade',
      fadeEffect: { crossFade: true },
      speed: 900,

      // ✅ only autoplay if there’s more than 1 slide
      autoplay: canLoop
        ? {
            delay: 4500,
            disableOnInteraction: false,
            // ⚠️ remove pauseOnMouseEnter so it doesn't pause immediately
            // pauseOnMouseEnter: true,
          }
        : false,

      pagination: {
        el: el.querySelector('.swiper-pagination'),
        clickable: true,
      },

      navigation: {
        nextEl: el.querySelector('.swiper-button-next'),
        prevEl: el.querySelector('.swiper-button-prev'),
      },

      on: {
        init() {
          // ✅ force start (fixes “autoplay not starting” edge cases)
          if (this.params.autoplay) this.autoplay.start()
        },
      },
    })

    // Optional: if only 1 slide, hide controls so it feels polished
    if (!canLoop) {
      const next = el.querySelector('.swiper-button-next')
      const prev = el.querySelector('.swiper-button-prev')
      const pag = el.querySelector('.swiper-pagination')
      if (next) next.style.display = 'none'
      if (prev) prev.style.display = 'none'
      if (pag) pag.style.display = 'none'
    }
  }

  // Mobile menu toggle
  const btn = document.getElementById('mobileMenuBtn')
  const menu = document.getElementById('mobileMenu')

  if (btn && menu) {
    btn.addEventListener('click', () => {
      menu.classList.toggle('hidden')
      const expanded = btn.getAttribute('aria-expanded') === 'true'
      btn.setAttribute('aria-expanded', String(!expanded))
      document.querySelectorAll('#mobileMenu a').forEach((a) => {
        a.addEventListener('click', () => menu.classList.add('hidden'))
      })
    })
  }
})
