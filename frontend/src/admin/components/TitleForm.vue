<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
import { useI18n } from 'vue-i18n';

const { t } = useI18n()
const props = defineProps<{
  title: string
  isCreateMode?: boolean
}>();
</script>
<template>
    <div class="card text-center">
        <h1 class="text-3xl">{{ `${capitalizeFirstLetter(t( props.title ))} (${isCreateMode ? t( 'create' ) : t( 'edit' )}) ` }}</h1>
    </div>
</template>