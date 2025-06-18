<script setup>
import { FilterMatchMode } from '@primevue/core/api'
import { useToast } from 'primevue/usetoast'
import { computed, onMounted, ref } from 'vue'
import { useAdminNewsStore } from '@/admin/stores/NewsAdminStore'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/shared/services/Translation'

const { t } = useI18n()
const newsAdminStore = useAdminNewsStore()
const currentLang = locale


const newsTransformed = computed(() => {
  return newsAdminStore.news.map((news) => ({
    ...news,
    titleTrans: currentLang.value === Languages.English ?
    news.title.en : news.title.es
  }))
})

onMounted(async () => {
  await newsAdminStore.getNews()
})

const toast = useToast()
const dt = ref()
const deleteNewsDialog = ref(false)
const deleteNewssDialog = ref(false)
const news = ref({})
const selectedNews = ref([])
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

function confirmDeleteNews(art) {
  news.value = art
  deleteNewsDialog.value = true
}

async function deleteNews(id) {
  deleteNewsDialog.value = false
    try {
    await newsAdminStore.deleteNews(id)

    showSuccessToast(toast, t, 'newsDeletedSuccessfully', 3000)
    news.value = {}
  } catch (err) {
    showErrorToast(toast, t, err, 'errorSavingNews')
  }
  news.value = {}
}

function confirmDeleteSelected() {
  deleteNewssDialog.value = true
}

async function deleteSelectedNewss() {
  try {
    const deletePromises = selectedNews.value.map(news => 
      newsAdminStore.deleteNews(news.id)
    );
    
    await Promise.all(deletePromises);
    
    await newsAdminStore.getNews();
    
    selectedNews.value = [];
    deleteNewssDialog.value = false;
    
    showSuccessToast(toast, t, 'newsDeleted', 3000);
  } catch (err) {
    showErrorToast(toast, t, err, 'errorDeletingNews');
  }
}
</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <RouterLink 
                :to="{ name: 'adminNewsCreate'}">
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
            :disabled="!selectedNews || !selectedNews.length"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedNews"
        :value="newsTransformed"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        :currentPageReportTemplate="capitalizeFirstLetter(t('showingNews'))"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">{{ capitalizeFirstLetter(t('manageNews')) }}</h4>
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
        <Column field="titleTrans" :header="capitalizeFirstLetter(t('title'))" sortable style="min-width: 12rem"></Column>
        <Column :header="capitalizeFirstLetter(t('image'))">
          <template #body="slotProps">
            <img
              :src="`${slotProps.data.image_url}`"
              :alt="slotProps.data.image_url"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <Column :header="capitalizeFirstLetter(t('actions'))" :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <RouterLink 
                :to="{ name: 'adminNewsEdit', params: { id: slotProps.data.id } }">
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
              @click="confirmDeleteNews(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog
      v-model:visible="deleteNewsDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="news"
          >{{ capitalizeFirstLetter(t('sureDelete')) }} <b>{{ news.nameTrans }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteNewsDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteNews(news.id)" />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="deleteNewssDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="news">{{ capitalizeFirstLetter(t('sureDeleteSelectedNews')) }}</span>
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteNewssDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteSelectedNewss" />
      </template>
    </Dialog>
  </div>
</template>
