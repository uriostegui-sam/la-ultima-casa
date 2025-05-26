<script setup lang="ts">
import Title from '@/components/Title.vue'
import Facebook from '@/assets/Icons/facebook.svg'
import Instagram from '@/assets/Icons/instagram.svg'
import WorldWideWebSVG from '@/assets/Icons/world-wide-web.svg'

const props = defineProps<{
  title?: string
  hasSubtitle?: boolean
  isWorkshop?: boolean
  isArtist?: boolean
  subtitle?: string
  type?: string
  coverImage?: string
  artist?: string
  date?: string
  price?: number
  description?: string
  instagram?: string
  facebook?: string
  website?: string
}>()

const socialLinks = [
  {
    name: 'facebook',
    icon: Facebook,
    url: props.facebook ? `https://www.facebook.com/${props.facebook}` : undefined,
    condition: !!props.facebook,
  },
  {
    name: 'instagram',
    icon: Instagram,
    url: props.instagram ? `https://www.instagram.com/${props.instagram}` : undefined,
    condition: !!props.instagram,
  },
  {
    name: 'website',
    icon: WorldWideWebSVG,
    url: typeof props.website === 'string' ? props.website : undefined,
    condition: typeof props.website === 'string',
  },
]
</script>

<template>
  <section class="px-10 mx-auto max-w-7xl pb-10">
    <Title
      :title="props.title"
      :has-subtitle="props.hasSubtitle"
      :subtitle="props.subtitle"
      :type="props.type"
    />
    <div class="flex gap-5 lg:gap-20 flex-col-reverse lg:flex-row">
      <div class="lg:flex-2/5 flex flex-col items-center">
        <!-- :src="props.coverImage" -->
        <img
          src="https://picsum.photos/200"
          :alt="props.title"
          class="w-full h-full object-cover"
        />
        <div
          v-if="props.isArtist"
          class="mt-3 bg-(--color-salmon) flex justify-around w-1/2 rounded-full py-2"
        >
          <template v-for="link in socialLinks" :key="link.name">
            <a v-if="link.condition" :href="link.url" target="_blank" rel="noopener noreferrer">
              <component :is="link.icon" class="w-7 h-7 fill-white" />
            </a>
          </template>
        </div>
      </div>
      <div v-if="props.isWorkshop" class="lg:flex-3/5">
        <p>
          {{ props.artist }}
        </p>
        <p>
          {{ props.date }}
        </p>
        <p>${{ props.price }} MXN</p>
        <p>
          {{ props.description }}
        </p>
      </div>
      <div v-if="!props.isWorkshop" class="lg:flex-3/5">
        <p>
          {{ props.description }}
        </p>
      </div>
    </div>
  </section>
</template>
