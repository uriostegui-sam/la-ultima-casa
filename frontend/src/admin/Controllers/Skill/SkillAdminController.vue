<script setup>
import { FilterMatchMode } from '@primevue/core/api'
import { useToast } from 'primevue/usetoast'
import { computed, onMounted, ref } from 'vue'
import { useAdminSkillStore } from '@/admin/stores/SkillAdminStore'
import { showErrorToast, showSuccessToast } from '@/admin/Services/Helpers'
import { capitalizeFirstLetter } from '@/shared/services/Helpers'
import { useI18n } from 'vue-i18n'
import { Languages, locale } from '@/shared/services/Translation'

const { t } = useI18n()
const skillAdminStore = useAdminSkillStore()
const currentLang = locale


const skillTransformed = computed(() => {
  return skillAdminStore.skills.map((skill) => ({
    ...skill,
    nameTrans: currentLang.value === Languages.English ?
      skill.name.en : skill.name.es
  }))
})

onMounted(async () => {
  await skillAdminStore.getSkills()
})

const toast = useToast()
const dt = ref()
const deleteSkillDialog = ref(false)
const deleteSkillsDialog = ref(false)
const skill = ref({})
const selectedSkills = ref([])
const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
})

function confirmDeleteSkill(art) {
  skill.value = art
  deleteSkillDialog.value = true
}

async function deleteSkill(id) {
  deleteSkillDialog.value = false
    try {
    await skillAdminStore.deleteSkill(id)

    showSuccessToast(toast, t, 'skillDeletedSuccessfully', 3000)
    skill.value = {}
  } catch (err) {
    showErrorToast(toast, t, err, 'errorSavingSkill')
  }
  skill.value = {}
}

function confirmDeleteSelected() {
  deleteSkillsDialog.value = true
}

async function deleteSelectedSkills() {
  try {
    const deletePromises = selectedSkills.value.map(skill => 
      skillAdminStore.deleteSkill(skill.id)
    );
    
    await Promise.all(deletePromises);
    
    await skillAdminStore.getSkills();
    
    selectedSkills.value = [];
    deleteSkillsDialog.value = false;
    
    showSuccessToast(toast, t, 'skillsDeleted', 3000);
  } catch (err) {
    showErrorToast(toast, t, err, 'errorDeletingSkills');
  }
}
</script>

<template>
  <div>
    <div class="card">
      <Toolbar class="mb-6">
        <template #start>
          <RouterLink 
                :to="{ name: 'adminSkillCreate'}">
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
            :disabled="!selectedSkills || !selectedSkills.length"
          />
        </template>
      </Toolbar>

      <DataTable
        ref="dt"
        v-model:selection="selectedSkills"
        :value="skillTransformed"
        dataKey="id"
        :paginator="true"
        :rows="10"
        :filters="filters"
        paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
        :rowsPerPageOptions="[5, 10, 25]"
        :currentPageReportTemplate="capitalizeFirstLetter(t('showingSkills'))"
      >
        <template #header>
          <div class="flex flex-wrap gap-2 items-center justify-between">
            <h4 class="m-0">{{ capitalizeFirstLetter(t('manageSkill')) }}</h4>
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
        <Column field="nameTrans" :header="capitalizeFirstLetter(t('name'))" sortable style="min-width: 16rem"></Column>
        <Column :header="capitalizeFirstLetter(t('image'))">
          <template #body="slotProps">
            <img
              :src="`https://estudiolaultimacasa.com/storage/${slotProps.data.profile_image}`"
              :alt="slotProps.data.profile_image"
              class="rounded"
              style="width: 64px"
            />
          </template>
        </Column>
        <Column :header="capitalizeFirstLetter(t('actions'))" :exportable="false" style="min-width: 12rem">
          <template #body="slotProps">
            <RouterLink 
                :to="{ name: 'adminSkillEdit', params: { id: slotProps.data.id } }">
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
              @click="confirmDeleteSkill(slotProps.data)"
            />
          </template>
        </Column>
      </DataTable>
    </div>

    <Dialog
      v-model:visible="deleteSkillDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="skill"
          >{{ capitalizeFirstLetter(t('sureDelete')) }} <b>{{ skill.nameTrans }}</b
          >?</span
        >
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteSkillDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteSkill(skill.id)" />
      </template>
    </Dialog>

    <Dialog
      v-model:visible="deleteSkillsDialog"
      :style="{ width: '450px' }"
      :header="capitalizeFirstLetter(t('confirm'))"
      :modal="true"
    >
      <div class="flex items-center gap-4">
        <i class="pi pi-exclamation-triangle !text-3xl" />
        <span v-if="skill">{{ capitalizeFirstLetter(t('sureDeleteSelectedSkills')) }}</span>
      </div>
      <template #footer>
        <Button :label="capitalizeFirstLetter(t('no'))" icon="pi pi-times" text @click="deleteSkillsDialog = false" />
        <Button :label="capitalizeFirstLetter(t('yes'))" icon="pi pi-check" @click="deleteSelectedSkills" />
      </template>
    </Dialog>
  </div>
</template>
