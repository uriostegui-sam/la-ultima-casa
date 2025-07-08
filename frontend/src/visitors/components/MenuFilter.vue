<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import ActionButton from './ActionButton.vue'
import { computed } from 'vue'

const { t } = useI18n()

const props = defineProps<{
  active?: string
  temporary?: boolean
  permanent?: boolean
}>()

const emit = defineEmits(['change'])

const activeFilter = computed({
  get: () => props.active,
  set: (val) => emit('change', val),
})

const options = computed(() => {
  const result = []

  if (props.permanent && props.temporary) result.push('all')
  if (props.permanent) result.push('permanent')
  if (props.temporary) result.push('temporary')

  return result
})

const handleFilterChange = (option: string) => {
  activeFilter.value = option
  emit('change', option)
}
</script>

<template>
  <div class="flex justify-center gap-3 lg:gap-x-20">
    <template v-for="option in options" :key="option">
      <ActionButton
        :is-filter="true"
        :active="activeFilter"
        :title="option"
        :filter="handleFilterChange"
      />
    </template>
  </div>
</template>
