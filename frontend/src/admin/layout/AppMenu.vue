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
            { label: capitalizeFirstLetter(t('aboutUs')), icon: 'pi pi-fw pi-id-card', to: '/admin/about-us' },
            { label: capitalizeFirstLetter(t('artists')), icon: 'pi pi-fw pi-palette', to: '/admin/artists' },
            { label: capitalizeFirstLetter(t('news')), icon: 'pi pi-fw pi-folder-open', to: '/admin/news', class: 'rotated-icon' },
            { label: capitalizeFirstLetter(t('workshops')), icon: 'pi pi-fw pi-wrench', to: '/admin/workshops' },
            { label: capitalizeFirstLetter(t('skills')), icon: 'pi pi-fw pi-paperclip', to: '/admin/skills' },
        ]
    },
    {
        // label: 'UI Components',
        // items: [
        //     { label: 'Form Layout', icon: 'pi pi-fw pi-id-card', to: '/admin/uikit/formlayout' },
        //     { label: 'Input', icon: 'pi pi-fw pi-check-square', to: '/admin/uikit/input' },
        //     { label: 'Button', icon: 'pi pi-fw pi-mobile', to: '/admin/uikit/button', class: 'rotated-icon' },
        //     { label: 'Table', icon: 'pi pi-fw pi-table', to: '/admin/uikit/table' },
        //     { label: 'List', icon: 'pi pi-fw pi-list', to: '/admin/uikit/list' },
        //     { label: 'Tree', icon: 'pi pi-fw pi-share-alt', to: '/admin/uikit/tree' },
        //     { label: 'Panel', icon: 'pi pi-fw pi-tablet', to: '/admin/uikit/panel' },
        //     { label: 'Overlay', icon: 'pi pi-fw pi-clone', to: '/admin/uikit/overlay' },
        //     { label: 'Media', icon: 'pi pi-fw pi-image', to: '/admin/uikit/media' },
        //     { label: 'Menu', icon: 'pi pi-fw pi-bars', to: '/admin/uikit/menu' },
        //     { label: 'Message', icon: 'pi pi-fw pi-comment', to: '/admin/uikit/message' },
        //     { label: 'File', icon: 'pi pi-fw pi-file', to: '/admin/uikit/file' },
        //     { label: 'Chart', icon: 'pi pi-fw pi-chart-bar', to: '/admin/uikit/charts' },
        //     { label: 'Timeline', icon: 'pi pi-fw pi-calendar', to: '/admin/uikit/timeline' },
        //     { label: 'Misc', icon: 'pi pi-fw pi-circle', to: '/admin/uikit/misc' }
        // ]
    },
    {
        label: 'Pages',
        icon: 'pi pi-fw pi-briefcase',
        to: '/admin/pages',
        items: [
            {
                label: 'Landing',
                icon: 'pi pi-fw pi-globe',
                to: '/admin/landing'
            },
            {
                label: 'Auth',
                icon: 'pi pi-fw pi-user',
                items: [
                    {
                        label: 'Login',
                        icon: 'pi pi-fw pi-sign-in',
                        to: '/admin/auth/login'
                    },
                    {
                        label: 'Error',
                        icon: 'pi pi-fw pi-times-circle',
                        to: '/admin/auth/error'
                    },
                    {
                        label: 'Access Denied',
                        icon: 'pi pi-fw pi-lock',
                        to: '/admin/auth/access'
                    }
                ]
            },
            {
                label: 'Crud',
                icon: 'pi pi-fw pi-pencil',
                to: '/admin/pages/crud'
            },
            {
                label: 'Not Found',
                icon: 'pi pi-fw pi-exclamation-circle',
                to: '/admin/pages/notfound'
            },
            {
                label: 'Empty',
                icon: 'pi pi-fw pi-circle-off',
                to: '/admin/pages/empty'
            }
        ]
    },
    {
        label: 'Hierarchy',
        items: [
            {
                label: 'Submenu 1',
                icon: 'pi pi-fw pi-bookmark',
                items: [
                    {
                        label: 'Submenu 1.1',
                        icon: 'pi pi-fw pi-bookmark',
                        items: [
                            { label: 'Submenu 1.1.1', icon: 'pi pi-fw pi-bookmark' },
                            { label: 'Submenu 1.1.2', icon: 'pi pi-fw pi-bookmark' },
                            { label: 'Submenu 1.1.3', icon: 'pi pi-fw pi-bookmark' }
                        ]
                    },
                    {
                        label: 'Submenu 1.2',
                        icon: 'pi pi-fw pi-bookmark',
                        items: [{ label: 'Submenu 1.2.1', icon: 'pi pi-fw pi-bookmark' }]
                    }
                ]
            },
            {
                label: 'Submenu 2',
                icon: 'pi pi-fw pi-bookmark',
                items: [
                    {
                        label: 'Submenu 2.1',
                        icon: 'pi pi-fw pi-bookmark',
                        items: [
                            { label: 'Submenu 2.1.1', icon: 'pi pi-fw pi-bookmark' },
                            { label: 'Submenu 2.1.2', icon: 'pi pi-fw pi-bookmark' }
                        ]
                    },
                    {
                        label: 'Submenu 2.2',
                        icon: 'pi pi-fw pi-bookmark',
                        items: [{ label: 'Submenu 2.2.1', icon: 'pi pi-fw pi-bookmark' }]
                    }
                ]
            }
        ]
    },
    {
        label: 'Get Started',
        items: [
            {
                label: 'Documentation',
                icon: 'pi pi-fw pi-book',
                to: '/documentation'
            },
            {
                label: 'View Source',
                icon: 'pi pi-fw pi-github',
                url: 'https://github.com/primefaces/sakai-vue',
                target: '_blank'
            }
        ]
    }
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
