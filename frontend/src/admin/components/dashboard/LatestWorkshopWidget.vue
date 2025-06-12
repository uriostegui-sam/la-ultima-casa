<script setup>
import { useAdminWorkshopStore } from '@/admin/stores/WorkshopAdminStore';
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
import { Languages, locale } from '@/shared/services/Translation';
import { computed, onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n()
const current = locale
const workshopStore = useAdminWorkshopStore()

const workshopTransformed = computed(() => {
    return workshopStore.workshops.map((workshop) => ({
      ...workshop,
    }))
})

onMounted(async () => {
  await workshopStore.getWorkshops()
});
</script>

<template>
    <div class="card">
        <div class="font-semibold text-xl mb-4">{{ capitalizeFirstLetter(t('latestWorkshops')) }}</div>
        <DataTable :value="workshopTransformed" :rows="5" :paginator="true" responsiveLayout="scroll">
            <Column style="width: 15%" :header="capitalizeFirstLetter(t('image'))">
                <template #body="slotProps">
                    <img :src="slotProps.data.cover_image_url" :alt="slotProps.data.cover_image_url" width="50" class="shadow" />
                </template>
            </Column>
            <Column field="title" :header="capitalizeFirstLetter(t('title'))" :sortable="true" style="width: 35%">
                <template #body="slotProps">
                    {{ current === Languages.English ? slotProps.data.title['en'] : slotProps.data.title['es'] }}
                </template>
            </Column>
            <Column field="type" :header="capitalizeFirstLetter(t('type'))" :sortable="true" style="width: 35%">
                <template #body="slotProps">
                    {{ capitalizeFirstLetter(t(slotProps.data.type)) }}
                </template>
            </Column>
            <Column style="width: 15%" :header="capitalizeFirstLetter(t('view'))">
                <template #body>
                    <Button icon="pi pi-search" type="button" class="p-button-text"></Button>
                </template>
            </Column>
        </DataTable>
    </div>
</template>
