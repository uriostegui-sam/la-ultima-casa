<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/shared/stores/AuthStore'
import OverlayPanel from 'primevue/overlaypanel'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const profileMenu = ref()
const authStore = useAuthStore()
const router = useRouter()

function toggleProfileMenu(event: MouseEvent) {
  profileMenu.value.toggle(event)
}

function logout() {
  authStore.logout()
  router.push('/admin/auth/login')
}

function goToProfile() {
  router.push('/admin/profile')
}
</script>

<template>
  <div class="layout-topbar-action" @click="toggleProfileMenu($event)">
    <i class="pi pi-user"></i>
    <span>{{ capitalizeFirstLetter(t('profile')) }}</span>

    <OverlayPanel ref="profileMenu">
      <ul class="list-none p-2">
        <li class="p-2 hover:bg-gray-100 cursor-pointer" @click="goToProfile">{{ capitalizeFirstLetter(t('profile')) }}</li>
        <li class="p-2 hover:bg-gray-100 cursor-pointer" @click="logout">{{ capitalizeFirstLetter(t('logout')) }}</li>
      </ul>
    </OverlayPanel>
  </div>
</template>