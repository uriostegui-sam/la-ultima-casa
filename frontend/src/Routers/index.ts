import Logo from '@/components/Logo.vue'
import HomeController from '@/Controllers/Visitors/Home/HomeController.vue'
import WorkshopController from '@/Controllers/Visitors/Workshop/WorkshopController.vue'
import WorkshopInfo from '../components/views/Visitors/Workshop/WorkshopInfo.vue'
import { createRouter, createWebHistory } from 'vue-router'
import NewsController from '@/Controllers/Visitors/News/NewsController.vue'
import NewsInfo from '@/components/views/Visitors/News/NewsInfo.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomeController,
  },
//   {
//     path: '/team',
//     name: 'Team',
//     component: () => import('@/views/Team.vue'),
//   },
//   {
//     path: '/team/:id',
//     name: 'TeamInfo',
//     component: () => import('@/views/Team.vue'),
//   },
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
    props: true
  },
  {
    path: '/news/:id',
    name: 'NewsInfo',
    component: NewsInfo,
    props: true
  },
//   // 404 fallback
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: Logo,
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
