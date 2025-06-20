<script setup>
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
import Logo from '@/visitors/components/Logo.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n()
const menuKeys = [
  { key: 'artists', href: '/admin/artists' },
  { key: 'news', href: '/admin/news' },
  { key: 'workshops', href: '/admin/workshops' },
]
</script>

<template>
    <div class="flex items-center justify-center min-h-screen overflow-hidden">
        <div class="flex flex-col items-center justify-center gap-5">
            <Logo />
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, color-mix(in srgb, var(--primary-color), transparent 60%) 10%, var(--surface-ground) 30%)">
                <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20 flex flex-col items-center" style="border-radius: 53px">
                    <span class="text-primary font-bold text-3xl">404</span>
                    <h1 class="text-surface-900 dark:text-surface-0 font-bold text-3xl lg:text-5xl mb-2">{{capitalizeFirstLetter(t("notFound"))}}</h1>
                    <div class="text-surface-600 dark:text-surface-200 mb-8">{{capitalizeFirstLetter(t("requestedNotAvailable"))}}.</div>
                    <template v-for="item in menuKeys" :key="item.key">
                        <router-link :to="item.href" class="w-full flex items-center py-8 border-surface-300 dark:border-surface-500 border-b">
                            <span class="flex justify-center items-center border-2 border-primary text-primary rounded-border" style="height: 3.5rem; width: 3.5rem">
                                <i class="pi pi-fw pi-table !text-2xl"></i>
                            </span>
                            <span class="ml-6 flex flex-col">
                                <span class="text-surface-900 dark:text-surface-0 lg:text-xl font-medium mb-0 block">{{capitalizeFirstLetter(t(item.key))}}</span>
                                <span class="text-surface-600 dark:text-surface-200 lg:text-xl">{{capitalizeFirstLetter(t(`goTo${capitalizeFirstLetter(item.key)}`))}}</span>
                            </span>
                        </router-link>
                    </template>
                    <div class="pt-8">
                        <Button as="router-link" label="Go to Dashboard" to="/admin" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
