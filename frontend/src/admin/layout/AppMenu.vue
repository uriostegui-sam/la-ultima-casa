<script setup>
import { ref } from 'vue';
const { t } = useI18n()
const current = locale

import AppMenuItem from './AppMenuItem.vue';
import { useI18n } from 'vue-i18n';
import { locale } from '@/shared/services/Translation';
import { capitalizeFirstLetter } from '@/shared/services/Helpers';

const model = ref([
    {
        label: 'Home',
        items: [{ label: 'Dashboard', icon: 'pi pi-fw pi-home', to: '/admin' }]
    },
    {
        label: 'Pages',
        items: [
            { label: capitalizeFirstLetter(t('navigation.aboutUs')), icon: 'pi pi-fw pi-id-card', to: '/admin/about-us' },
            { label: capitalizeFirstLetter(t('artists.artists')), icon: 'pi pi-fw pi-palette', to: '/admin/artists' },
            { label: capitalizeFirstLetter(t('news.news')), icon: 'pi pi-fw pi-folder-open', to: '/admin/news', class: 'rotated-icon' },
            { label: capitalizeFirstLetter(t('workshop.workshops')), icon: 'pi pi-fw pi-wrench', to: '/admin/workshops' },
            { label: capitalizeFirstLetter(t('skills.skills')), icon: 'pi pi-fw pi-paperclip', to: '/admin/skills' },
        ]
    },
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
