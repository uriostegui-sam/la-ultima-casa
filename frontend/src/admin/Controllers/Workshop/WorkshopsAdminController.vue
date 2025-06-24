<script setup>
import { FilterMatchMode } from '@primevue/core/api'
import { useToast } from 'primevue/usetoast'
import { computed, onMounted, ref } from 'vue'
import { useAdminWorkshopStore } from '@/admin/stores/WorkshopAdminStore'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/shared/services/Translation'

const { t } = useI18n()
const workshopAdminStore = useAdminWorkshopStore()
const currentLang = locale

const workshopTransformed = computed(() =>
  workshopAdminStore.workshops.map((workshop) => {
    const today = new Date()
    let status

    if (workshop.type === 'permanent') {
      status = capitalizeFirstLetter(t('ongoing'))
    } else if (workshop.start_date && new Date(workshop.start_date) > today) {
      status = capitalizeFirstLetter(t('upcoming'))
    } else if (workshop.end_date && new Date(workshop.end_date) < today) {
      status = capitalizeFirstLetter(t('ended'))
    } else {
      status = capitalizeFirstLetter(t('ongoing'))
    }
    return {
      ...workshop,
      titleTranslated:
        currentLang.value === Languages.English ? workshop.title.en : workshop.title.es,
      dateTranslated: {
        start: new Date(workshop.start_date).toLocaleDateString(),
        end:
          workshop.type === 'permanent'
            ? capitalizeFirstLetter(t('permanentSingular'))
            : new Date(workshop.end_date).toLocaleDateString(),
      },
      status,
    }
  }),
)

onMounted(async () => {
  await workshopAdminStore.getWorkshops()
})

const toast = useToast()
const dt = ref()
const deleteWorkshopDialog = ref(false)
const deleteWorkshopsDialog = ref(false)
const workshop = ref({})
const selectedWorkshops = ref([])
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

function confirmDeleteWorkshop(art) {
  workshop.value = art
  deleteWorkshopDialog.value = true
}

async function deleteWorkshop(id) {
  deleteWorkshopDialog.value = false
  try {
    await workshopAdminStore.deleteWorkshop(id)

    showSuccessToast(toast, t, 'workshopDeletedSuccessfully', 3000)
    workshop.value = {}
  } catch (err) {
    showErrorToast(toast, t, err, 'errorSavingWorkshop')
  }
  workshop.value = {}
}

function confirmDeleteSelected() {
  deleteWorkshopsDialog.value = true
}

async function deleteSelectedWorkshops() {
  try {
    const deletePromises = selectedWorkshops.value.map((workshop) =>
      workshopAdminStore.deleteWorkshop(workshop.id),
    )

    await Promise.all(deletePromises)

    await workshopAdminStore.getWorkshops()

    selectedWorkshops.value = []
    deleteWorkshopsDialog.value = false

    showSuccessToast(toast, t, 'workshopsDeleted', 3000)
  } catch (err) {
    showErrorToast(toast, t, err, 'errorDeletingWorkshops')
  }
}
</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <RouterLink :to="{ name: 'adminWorkshopCreate' }">
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
            :disabled="!selectedWorkshops || !selectedWorkshops.length"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedWorkshops"
        :value="workshopTransformed"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        :currentPageReportTemplate="capitalizeFirstLetter(t('showingWorkshops'))"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">{{ capitalizeFirstLetter(t('manageWorkshop')) }}</h4>
            <IconField>
              <InputIcon>
                <i class="pi pi-search" />
              </InputIcon>
              <InputText
                v-model="filters['global'].value"
                :placeholder="`${capitalizeFirstLetter(t('search'))} ...`"
              />
            </IconField>
          </div>
        </template>

        <Column selectionMode="multiple" style="width: 3rem" :exportable="false"></Column>
        <Column field="id" header="ID" sortable style="min-width: 2rem"></Column>
        <Column
          field="status"
          :header="capitalizeFirstLetter(t('status'))"
          sortable
          style="min-width: 2rem"
        ></Column>
        <Column
          field="titleTranslated"
          :header="capitalizeFirstLetter(t('title'))"
          sortable
          style="min-width: 16rem"
        ></Column>
        <Column :header="capitalizeFirstLetter(t('image'))">
          <template #body="slotProps">
            <img
              :src="`https://estudiolaultimacasa/storage/${slotProps.data.cover_image_path}`"
              :alt="slotProps.data.titleTranslated"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <Column
          field="dateTranslated.start"
          :header="capitalizeFirstLetter(t('startDate'))"
          style="min-width: 8rem"
        ></Column>
        <Column
          field="dateTranslated.end"
          :header="capitalizeFirstLetter(t('endDate'))"
          sortable
          style="min-width: 8rem"
        ></Column>
        <Column
          field="price"
          :header="capitalizeFirstLetter(t('price'))"
          sortable
          style="min-width: 6rem"
        ></Column>
        <Column
          field="artist.name"
          :header="capitalizeFirstLetter(t('artist'))"
          sortable
          style="min-width: 12rem"
        ></Column>
        <Column
          :header="capitalizeFirstLetter(t('actions'))"
          :exportable="false"
          style="min-width: 12rem"
        >
          <template #body="slotProps">
            <RouterLink :to="{ name: 'adminWorkshopEdit', params: { id: slotProps.data.id } }">
              <Button icon="pi pi-pencil" outlined rounded class="mr-2" />
            </RouterLink>
            <Button
              icon="pi pi-trash"
              outlined
              rounded
              severity="danger"
              @click="confirmDeleteWorkshop(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog
      v-model:visible="deleteWorkshopDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="workshop"
          >{{ capitalizeFirstLetter(t('sureDelete')) }} <b>{{ workshop.name }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button
          :label="capitalizeFirstLetter(t('no'))"
          icon="pi pi-times"
          text
          @click="deleteWorkshopDialog = false"
        />
        <Button
          :label="capitalizeFirstLetter(t('yes'))"
          icon="pi pi-check"
          @click="deleteWorkshop(workshop.id)"
        />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="deleteWorkshopsDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="workshop">{{ capitalizeFirstLetter(t('sureDeleteSelectedWorkshops')) }}</span>
      </div>
      <template #footer>
        <Button
          :label="capitalizeFirstLetter(t('no'))"
          icon="pi pi-times"
          text
          @click="deleteWorkshopsDialog = false"
        />
        <Button
          :label="capitalizeFirstLetter(t('yes'))"
          icon="pi pi-check"
          @click="deleteSelectedWorkshops"
        />
      </template>
    </Dialog>
  </div>
</template>
