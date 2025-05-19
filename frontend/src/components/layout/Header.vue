<script setup lang="ts">
import { ref } from 'vue'
import { Dialog, DialogPanel, PopoverGroup } from '@headlessui/vue'
import { Bars3Icon, XMarkIcon } from '@heroicons/vue/24/outline'
import LanguageToggle from '../LanguageToggle.vue'
import Logo from '../Logo.vue'
import NavLink from '../NavLink.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const menuKeys = [
  { key: 'aboutUs', href: '/nosotros' },
  { key: 'artists', href: '/artistas' },
  { key: 'news', href: '/noticias' },
  { key: 'workshops', href: '/workshops' },
  { key: 'contact', href: '/contacto' },
]

const mobileMenuOpen = ref(false)
</script>

<template>
  <header>
    <nav class="mx-auto max-w-7xl p-6 lg:px-8" aria-label="Global">
      <div class="flex items-center justify-between">
        <div class="flex lg:flex-1">
          <Logo :header="true" />
        </div>
        <div class="flex lg:hidden">
          <button
            type="button"
            class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
            @click="mobileMenuOpen = true"
          >
            <span class="sr-only">{{ $t('openMainMenu') }}</span>
            <Bars3Icon class="size-6" aria-hidden="true" />
          </button>
        </div>
        <PopoverGroup class="hidden lg:flex lg:gap-x-12">
          <div>
            <h1>LA ÚLTIMA CASA</h1>
            <h3 class="font-logo-sub text-center">{{ $t('artStudio') }}</h3>
          </div>
        </PopoverGroup>
        <div class="hidden lg:flex lg:flex-1 lg:justify-end">
          <LanguageToggle :header="true" />
        </div>
      </div>
      <div class="hidden lg:flex lg:items-center lg:justify-around lg:max-w-2xl mx-auto mt-7">
        <template v-for="(item, index) in menuKeys" :key="item.key">
          <router-link :to="item.href" custom v-slot="{ href, navigate, isActive }">
            <NavLink :active="isActive" :href="href" :navigate="(e) => { console.log('Navigating'); navigate(e) }">
              {{ t(item.key) }}
            </NavLink>
          </router-link>
          <span v-if="index < menuKeys.length - 1" class="text-(--color-salmon)">|</span>
        </template>
      </div>
    </nav>
    <Dialog class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
      <div class="fixed inset-0 z-10" />
      <DialogPanel
        class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10"
      >
        <div class="flex items-center justify-between">
          <Logo :header="true" />
          <button
            type="button"
            class="-m-2.5 rounded-md p-2.5 text-gray-700"
            @click="mobileMenuOpen = false"
          >
            <span class="sr-only">{{ $t('closeMenu') }}</span>
            <XMarkIcon class="size-6" aria-hidden="true" />
          </button>
        </div>
        <div class="mt-6 flow-root">
          <div class="-my-6 divide-y divide-gray-500/10">
            <div class="space-y-2 py-6">
              <template v-for="item in menuKeys" :key="item.key">
                <router-link :to="item.href" custom v-slot="{ href, navigate, isActive }">
                  <nav-link
                    :active="isActive"
                    :href="href"
                    :navigate="navigate"
                    class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                  >
                    {{ t(item.key) }}
                  </nav-link>
                </router-link>
              </template>
            </div>
            <LanguageToggle :header="true" />
          </div>
        </div>
      </DialogPanel>
    </Dialog>
  </header>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Aoboshi+One&family=Nanum+Pen+Script&display=swap');

h1 {
  font-family: 'NATS-Regular', sans-serif;
  font-size: 48px;
  font-style: normal;
  line-height: 40px;
}

h3 {
  font-size: 32px;
  font-style: normal;
  font-weight: 400;
  line-height: 40px; /* 125% */
}
</style>
