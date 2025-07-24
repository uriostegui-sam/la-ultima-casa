<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import LanguageToggle from '../LanguageToggle.vue'
import Logo from '../Logo.vue'
import { useI18n } from 'vue-i18n'
import { computed, nextTick, onMounted, ref } from 'vue'
import { useAdminAboutUsStore } from '@/admin/stores/AboutUsAdminStore'
import type { AboutUs } from '@/shared/Interfaces/AboutUs'
import LoadingComponent from '@/shared/components/LoadingComponent.vue'
const { t } = useI18n()

const aboutUsStore = useAdminAboutUsStore()
const existingId = computed(() => {
  return Number(aboutUsStore.aboutUs.length > 0 ? aboutUsStore.aboutUs[0].id : null)
})
const aboutUs = ref<AboutUs | null>(null)
const iframeContainer = ref<HTMLElement | null>(null)

onMounted(async () => {
  await aboutUsStore.getAboutUs()
  
  if (existingId.value) {
    await aboutUsStore.getAboutUsById(existingId.value)
    
    aboutUs.value = aboutUsStore.selectedAboutUs
  }

  await nextTick()

  const iframe = iframeContainer.value?.querySelector('iframe')
  if (iframe) {
    iframe.style.height = '100%'
    iframe.style.width = '100%'
  }
})

const formattedAddress = computed(() => {
  return aboutUs.value?.address?.text?.replace(/\r\n/g, '<br>') ?? ''
})

const aboutKeys = [
  { key: 'whoWeAre', href: '/' },
  { key: 'courses', href: '/workshops' },
]

const contactKeys = computed(() => [
  { key: aboutUs.value?.mail ?? '' },
  { key: aboutUs.value?.number ?? '' },
])
</script>

<template>
  <div v-if="aboutUs" class="mt-8">
    <footer class="bg-(--color-salmon)">
      <div class="mx-auto w-full max-w-screen-xl text-white px-6 lg:px-8 text-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 py-6">
          <div class="flex flex-col items-center md:items-start gap-4 px-4">
            <div class="flex md:flex-col items-center justify-around w-full h-full">
              <Logo class="justify-self-start" />
              <div>
                <div class="flex justify-around w-full md:h-full md:w-auto gap-4">
                  <a
                    href="https://www.facebook.com/estudiolaultimacasa/"
                    target="_blank"
                    rel="noopener"
                  >
                    <div class="facebook"></div>
                  </a>
                  <a
                    href="https://www.instagram.com/estudiolaultimacasa/"
                    target="_blank"
                    rel="noopener"
                  >
                    <div class="insta"></div>
                  </a>
                </div>
                <LanguageToggle />
              </div>
            </div>
            <div class="self-start md:hidden block">
              <h2 class="mb-2 uppercase font-bold">{{ capitalizeFirstLetter($t('aboutUs')) }}</h2>
              <ul>
                <template v-for="item in aboutKeys" :key="item.key">
                  <li>
                    <a :href="item.href" class="hover:underline">
                      {{ capitalizeFirstLetter(t(item.key)) }}
                    </a>
                  </li>
                </template>
              </ul>
              <h2 class="mb-1 mt-4 uppercase font-bold">
                {{ capitalizeFirstLetter($t('contactUs')) }}
              </h2>
              <ul>
                <template v-for="item in contactKeys" :key="item.key">
                  <li>
                    <p class="hover:underline">{{ item.key }}</p>
                  </li>
                </template>
              </ul>
            </div>
          </div>

          <div class="px-4 hidden md:block">
            <h2 class="mb-2 uppercase font-bold">{{ capitalizeFirstLetter($t('aboutUs')) }}</h2>
            <ul>
              <template v-for="item in aboutKeys" :key="item.key">
                <li>
                  <a :href="item.href" class="hover:underline">
                    {{ capitalizeFirstLetter(t(item.key)) }}
                  </a>
                </li>
              </template>
            </ul>
            <h2 class="mb-1 mt-4 uppercase font-bold">
              {{ capitalizeFirstLetter($t('contactUs')) }}
            </h2>
            <ul>
              <template v-for="item in contactKeys" :key="item.key">
                <li>
                  <p class="hover:underline">{{ item.key }}</p>
                </li>
              </template>
            </ul>
            <h2 class="mb-1 mt-4 uppercase font-bold">{{ capitalizeFirstLetter($t('comeMeetUs')) }}</h2>
            <p v-html="formattedAddress"></p>
          </div>

          <div class="flex flex-col justify-between px-4">
            <div class="md:hidden block">
              <h2 class="mb-1 mt-4 uppercase font-bold">{{ capitalizeFirstLetter($t('comeMeetUs')) }}</h2>
              <p class="mb-4" v-html="formattedAddress"></p>       
            </div>
            <div ref="iframeContainer" v-html="aboutUs.address.map" class="box-content h-full"></div>
          </div>
        </div>

        <div class="px-2 pb-6 text-center">
          <span><a href="#">SamyyUV ©</a> - 2025</span>
        </div>
      </div>
    </footer>
  </div>
  <div v-else>
    <LoadingComponent />
  </div>
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
  background-color: var(--color-cream);
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
