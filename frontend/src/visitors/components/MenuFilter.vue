<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import ActionButton from './ActionButton.vue';
import { ref } from 'vue'

const { t } = useI18n()

const props = defineProps<{
  active?: string
}>()

const emit = defineEmits(['change'])

const activeFilter = ref(props.active || 'all')
const options = ['all', 'permanent', 'temporary']

const handleFilterChange = (option: string) => {
  activeFilter.value = option
  emit('change', option)
}
</script>

<template>
  <div class="flex justify-center gap-3 lg:gap-x-20">
    <ActionButton
      v-for="option in options"
      :is-filter="true"
      :active="activeFilter"
      :key="option"
      :title="option"
      :filter="handleFilterChange"
    />
  </div>
</template>
