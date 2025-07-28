<script setup>
import LatestWorkshopWidget from '@/admin/components/dashboard/LatestWorkshopWidget.vue';
import DashboadCountWidget from '../components/dashboard/DashboadCountWidget.vue';
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
import { useI18n } from 'vue-i18n';
import { useAdminArtistStore } from '../stores/ArtistAdminStore';
import { useAdminArtworkStore } from '../stores/ArtworkAdminStore';
import { useAdminWorkshopStore } from '../stores/WorkshopAdminStore';
import { onMounted, ref } from 'vue';
import LoadingComponent from '@/shared/components/LoadingComponent.vue';

const { t } = useI18n();
const artistAdminStore = useAdminArtistStore();
const artworkAdminStore = useAdminArtworkStore();
const workshopAdminStore = useAdminWorkshopStore();
const artists = ref(0);
const artwork = ref(0);
const workshops = ref(0);

const loading = ref(true);

onMounted(async () => {
    await artistAdminStore.getArtists();
    await artworkAdminStore.getArtworks();
    await workshopAdminStore.getWorkshops();

    artists.value = artistAdminStore.artists.length;
    artwork.value = artworkAdminStore.artworks.length;
    workshops.value = workshopAdminStore.workshops.length;

    loading.value = false;
});
</script>

<template>
    <div v-if="!loading"
        class="grid grid-cols-12 gap-8">

        <div class="col-span-12 flex justify-between">
            <DashboadCountWidget icon="pi-palette" :title="capitalizeFirstLetter(t('artists.artists'))" :count="artists"/>
            <DashboadCountWidget icon="pi-pen-to-square" :title="capitalizeFirstLetter(t('artworks.artwork'))" :count="artwork"/>
            <DashboadCountWidget icon="pi-wrench" :title="capitalizeFirstLetter(t('workshop.workshops'))" :count="workshops"/>
        </div>
        <div class="col-span-12">
            <LatestWorkshopWidget />
        </div>
    </div>
    <div v-else><LoadingComponent /></div>
</template>
