<script setup>
import { useLayout } from '@/admin/layout/composables/layout';
import AppConfigurator from './AppConfigurator.vue';
import ProfileMenu from '../components/ProfileMenu.vue';
import LanguageToggle from '../components/LanguageToggle.vue';
import Logo from '@/visitors/components/Logo.vue'

const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <router-link to="/admin" class="layout-topbar-logo">
                <Logo class="w-13" />
                <span>LA ÚLTIMA CASA</span>
            </router-link>
        </div>

        <div class="layout-topbar-actions">
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
                <div class="relative">
                    <button
                        v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
                        type="button"
                        class="layout-topbar-action layout-topbar-action-highlight"
                    >
                        <i class="pi pi-palette"></i>
                    </button>
                    <AppConfigurator />
                </div>
            </div>

            <button
                class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ selector: '@next', enterFromClass: 'hidden', enterActiveClass: 'animate-scalein', leaveToClass: 'hidden', leaveActiveClass: 'animate-fadeout', hideOnOutsideClick: true }"
            >
                <i class="pi pi-ellipsis-v"></i>
            </button>

            <div class="layout-topbar-menu hidden lg:block">
                <div class="layout-topbar-menu-content">
                    
                    <ProfileMenu/>
                    <div class="languageToggle">
                        <LanguageToggle/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scroped>
.languageToggle {
    display: inline-flex;
    border-radius: 4rem;
    border: 1px solid black;
}
</style>