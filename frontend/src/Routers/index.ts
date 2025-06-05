import AppLayout from '@/admin/layout/AppLayout.vue'
import Logo from '@/visitors/components/Logo.vue'
import ArtistController from '@/visitors/Controllers/Artists/ArtistController.vue'
import HomeController from '@/visitors/Controllers/Home/HomeController.vue'
import NewsController from '@/visitors/Controllers/News/NewsController.vue'
import WorkshopController from '@/visitors/Controllers/Workshop/WorkshopController.vue'
import ArtistInfo from '@/visitors/views/Artists/ArtistInfo.vue'
import NewsInfo from '@/visitors/views/News/NewsInfo.vue'
import VisitorLayout from '@/visitors/views/VisitorLayout.vue'
import WorkshopInfo from '@/visitors/views/Workshop/WorkshopInfo.vue'
import ArtistAdminController from '@/admin/Controllers/Artists/ArtistAdminController.vue'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    component: VisitorLayout,
    children: [
      {
        path: '/',
        name: 'Home',
        component: HomeController,
        meta: { exact: true },
      },
      {
        path: '/artists',
        name: 'Artist',
        component: ArtistController,
      },
      {
        path: '/artists/:id',
        name: 'ArtistInfo',
        component: ArtistInfo,
      },
      {
        path: '/workshops',
        name: 'Workshop',
        component: WorkshopController,
      },
      {
        path: '/workshops/:id',
        name: 'WorkshopInfo',
        component: WorkshopInfo,
      },
      {
        path: '/news',
        name: 'News',
        component: NewsController,
        props: true,
      },
      {
        path: '/news/:id',
        name: 'NewsInfo',
        component: NewsInfo,
        props: true,
      },
    ],
  },
  {
    path: '/admin',
    component: AppLayout,
    children: [
      {
        path: '/admin',
        name: 'dashboard',
        component: () => import('@/admin/views/Dashboard.vue'),
      },
      {
        path: '/admin/artists',
        name: 'adminArtist',
        component: ArtistAdminController,
      },
    ],
  },
  //   // 404 fallback
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: Logo,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
