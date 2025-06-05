<script setup>
import LatestWorkshopWidget from '@/admin/components/dashboard/LatestWorkshopWidget.vue';
</script>

<template>
    <div class="grid grid-cols-12 gap-8">

        <div class="col-span-12 xl:col-span-6">
            <LatestWorkshopWidget />
            <LatestWorkshopWidget />
        </div>
        <div class="col-span-12 xl:col-span-6">
            <LatestWorkshopWidget />
            <LatestWorkshopWidget />
        </div>
    </div>
</template>
