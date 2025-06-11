<script setup>
import { FilterMatchMode } from '@primevue/core/api'
import { useToast } from 'primevue/usetoast'
import { computed, onMounted, ref } from 'vue'
import { useAdminArtistStore } from '@/admin/stores/ArtistAdminStore'
import { getLanguageStatus, getSocialLinksStatus, showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
const artistAdminStore = useAdminArtistStore()

const artistTransformed = computed(() => {
  return artistAdminStore.artists.map((artist) => ({
    ...artist,
    minibioStatus: getLanguageStatus(artist.minibio),
    bioStatus: getLanguageStatus(artist.bio),
    socialLinksStatus: getSocialLinksStatus(artist.social_links).split(' '),
    hasSkills: artist.skills?.length > 0 ? '✅' : '❌',
  }))
})

onMounted(async () => {
  await artistAdminStore.getArtists()
})

const toast = useToast()
const dt = ref()
const artists = ref([])
const artistDialog = ref(false)
const deleteArtistDialog = ref(false)
const deleteArtistsDialog = ref(false)
const artist = ref({})
const selectedArtists = ref([])
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})
const submitted = ref(false)

function confirmDeleteArtist(art) {
  artist.value = art
  deleteArtistDialog.value = true
}

async function deleteArtist(id) {
  deleteArtistDialog.value = false
    try {
    await artistAdminStore.deleteArtist(id)

    showSuccessToast(toast, t, 'artistDeletedSuccessfully', 3000)
    artist.value = {}
  } catch (err) {
    showErrorToast(toast, t, err, 'errorSavingArtist')
  }
  artist.value = {}
}

function confirmDeleteSelected() {
  deleteArtistsDialog.value = true
}

async function deleteSelectedArtists() {
  try {
    const deletePromises = selectedArtists.value.map(artist => 
      artistAdminStore.deleteArtist(artist.id)
    );
    
    await Promise.all(deletePromises);
    
    await artistAdminStore.getArtists();
    
    selectedArtists.value = [];
    deleteArtistsDialog.value = false;
    
    showSuccessToast(toast, t, 'artistsDeleted', 3000);
  } catch (err) {
    showErrorToast(toast, t, err, 'errorDeletingArtists');
  }
}
</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <RouterLink 
                :to="{ name: 'adminArtistCreate'}">
          <Button
            :label="capitalizeFirstLetter(t('new'))"
            icon="pi pi-plus"
            severity="secondary"
            class="mr-2"
            @click="openNew"
          />
          </RouterLink>
          <Button
            :label="capitalizeFirstLetter(t('delete'))"
            icon="pi pi-trash"
            severity="secondary"
            @click="confirmDeleteSelected"
            :disabled="!selectedArtists || !selectedArtists.length"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedArtists"
        :value="artistTransformed"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        :currentPageReportTemplate="capitalizeFirstLetter(t('showingArtists'))"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">{{ capitalizeFirstLetter(t('manageArtist')) }}</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText v-model="filters['global'].value" :placeholder="`${capitalizeFirstLetter(t('search'))} ...`" />
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="id" header="ID" sortable style="min-width: 2rem"></Column>
        <Column field="name" :header="capitalizeFirstLetter(t('name'))" sortable style="min-width: 16rem"></Column>
        <Column :header="capitalizeFirstLetter(t('image'))">
          <template #body="slotProps">
            <img
              :src="slotProps.data.profile_image_url"
              :alt="slotProps.data.profile_image_url"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <Column field="minibio" :header="capitalizeFirstLetter(t('minibio'))" style="min-width: 8rem">
          <template #body="slotProps">
            {{ slotProps.data.minibioStatus }}
          </template>
        </Column>
        <Column field="bio" :header="capitalizeFirstLetter(t('bio'))" sortable style="min-width: 8rem">
          <template #body="slotProps">
            {{ slotProps.data.bioStatus }}
          </template>
        </Column>
        <Column field="social_links" :header="capitalizeFirstLetter(t('social'))" sortable style="min-width: 12rem">
          <template #body="slotProps">
            <Button
              v-for="socialLink of slotProps.data.socialLinksStatus"
              :icon="`pi ${socialLink}`"
              outlined
              rounded
              severity="secondary"
              class="mr-2"
            />           
          </template>
        </Column>
        <Column :header="capitalizeFirstLetter(t('actions'))" :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <RouterLink 
                :to="{ name: 'adminArtistEdit', params: { id: slotProps.data.id } }">
                <Button
                icon="pi pi-pencil"
                outlined
                rounded
                class="mr-2"
                />
            </RouterLink>
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteArtist(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog
      v-model:visible="deleteArtistDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="artist"
          >{{ capitalizeFirstLetter(t('sureDelete')) }} <b>{{ artist.name }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteArtistDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteArtist(artist.id)" />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="deleteArtistsDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="artist">{{ capitalizeFirstLetter(t('sureDeleteSelectedArtists')) }}</span>
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteArtistsDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteSelectedArtists" />
      </template>
    </Dialog>
  </div>
</template>
