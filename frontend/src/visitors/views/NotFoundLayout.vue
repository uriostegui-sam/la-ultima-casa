<script setup lang="ts">
import { capitalizeFirstLetter } from '@/shared/services/Helpers';
</script>

<template>
    <div class="flex flex-col items-center justify-center pt-10">
        <img
           src="@/visitors/assets/Images/error_404.png"
           alt="Error 404"
           class="w-1/2 h-1/2 object-cover"
         />
         <div class="text-center text-2xl font-bold mt-4">
           {{ (capitalizeFirstLetter($t('commun.errorNotFound'))) }}.
         </div>
    </div>
</template>

<style scoped></style>
