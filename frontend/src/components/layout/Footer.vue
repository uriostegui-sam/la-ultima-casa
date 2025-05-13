<script setup lang="ts">
import LanguageToggle from '../LanguageToggle.vue'
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const aboutKeys = [
  { key: 'whoWeAre', href: '/artistas' },
  { key: 'courses', href: '/cursos' },
  { key: 'joinTeam', href: '/contacto' },
]

const contactKeys = [
  { key: 'hola@laultimacasa.com', href: '/mail' },
  { key: '044 312 119 5692', href: '/phone' },
]
</script>

<template>
  <footer class="bg-(--color-salmon)">
    <div class="mx-auto w-full max-w-screen-xl text-white px-6 lg:px-8">
      <div class="grid grid-cols-1 gap-8 px-4 py-6 lg:py-8 md:grid-cols-3">
        <div class="flex flex-col items-center justify-between">
          <a href="#">
            <span class="sr-only">La Última Casa</span>
            <img
              class="h-45 w-auto"
              src="@/assets/images/logo_UC.svg"
              alt="logo de la última casa"
            />
          </a>
          <div class="flex w-32 justify-around">
            <a href="https://www.facebook.com/estudiolaultimacasa/" target="_blank" rel="noopener">
              <div class="facebook"></div>
            </a>
            <a href="https://www.instagram.com/estudiolaultimacasa/" target="_blank" rel="noopener">
              <div class="insta"></div>
            </a>
          </div>
          <LanguageToggle />
        </div>
        <div>
          <h2 class="mb-2 uppercase font-bold">{{ $t('aboutUs') }}</h2>
          <ul>
            <template v-for="item in aboutKeys" :key="item.key">
              <li>
                <a :href="item.href" class="hover:underline">{{ t(item.key) }}</a>
              </li>
            </template>
          </ul>
          <h2 class="mb-2 mt-8 uppercase font-bold">{{ $t('contactUs') }}</h2>
          <ul>
            <template v-for="item in contactKeys" :key="item.key">
              <li>
                <a :href="item.href" class="hover:underline">{{ t(item.key) }}</a>
              </li>
            </template>
          </ul>
        </div>
        <div class="flex flex-col justify-between">
          <h2 class="mb-2 uppercase font-bold">{{ $t('comeMeetUs') }}</h2>
          <p>
            Palma Kerpis 152 <br />
            Colinas de Santa Barbara <br />
            28017 Colima, Col., Mexico
          </p>
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3766.4391631193075!2d-103.7204535232921!3d19.263259146064232!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x84255b09965e0ce3%3A0xc086d17bda474fa2!2sEstudio%20La%20%C3%9Altima%20Casa!5e0!3m2!1sen!2sfr!4v1747160062015!5m2!1sen!2sfr"
            width="220"
            height="170"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
      <div class="px-2 py-6 text-center">
        <span class=""><a href="https://flowbite.com/">SamyyUV ©</a> - 2025 </span>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.insta,
.facebook {
  width: 24px;
  height: 24px;
  background-color: white;
  display: inline-block;
  mask-repeat: no-repeat;
  mask-position: center;
  mask-size: contain;
  -webkit-mask-repeat: no-repeat;
  -webkit-mask-position: center;
  -webkit-mask-size: contain;
}

.insta:hover,
.facebook:hover {
  background-color: var(--color-teal);
}

.insta {
  mask-image: url('../../assets/Icons/instagram.svg');
  -webkit-mask-image: url('../../assets/Icons/instagram.svg');
}

.facebook {
  mask-image: url('../../assets/Icons/facebook.svg');
  -webkit-mask-image: url('../../assets/Icons/facebook.svg');
}
</style>
