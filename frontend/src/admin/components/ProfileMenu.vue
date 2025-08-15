<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/shared/stores/AuthStore'
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
  const artistId = authStore.user?.artist?.id
  if (artistId) {
    router.push(`/admin/artists/edit/${artistId}`)
  } else {
    router.push('/admin/artists')
  }
}
</script>

<template>
  <div class="layout-topbar-action" @click="toggleProfileMenu($event)">
    <i class="pi pi-user"></i>
    <span>{{ capitalizeFirstLetter(t('authentication.profile')) }}</span>

    <Popover ref="profileMenu">
      <ul class="list-none p-2">
        <li class="p-2 hover:bg-gray-100 cursor-pointer" @click="goToProfile">{{ capitalizeFirstLetter(t('artists.profileArtist')) }}</li>
        <router-link to="/admin/user" class="layout-topbar-logo">
          <li class="p-2 hover:bg-gray-100 cursor-pointer">{{ capitalizeFirstLetter(t('divers.profileUser')) }}</li>
        </router-link>
        <li class="p-2 hover:bg-gray-100 cursor-pointer" @click="logout">{{ capitalizeFirstLetter(t('authentication.logout')) }}</li>
      </ul>
    </Popover>
  </div>
</template>