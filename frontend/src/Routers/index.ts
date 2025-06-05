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
import Login from '@/admin/views/pages/auth/Login.vue'
import { useAuthStore } from '@/shared/stores/AuthStore'
import ArtistForm from '@/admin/views/pages/ArtistForm.vue'
import ArtworkForm from '@/admin/views/pages/ArtworkForm.vue'

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
    meta: { requiresAuth: true, requiresAdmin: true },
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
      {
        path: '/admin/artists/edit/:id',
        name: 'adminArtistEdit',
        component: ArtistForm,
      },
      {
        path: '/admin/artists/:id/artwork/edit/:id',
        name: 'adminArtistArtworkEdit',
        component: ArtworkForm,
      },
    ],
  },
  {
    path: '/admin/auth/login',
    name: 'login',
    component: Login
  },
  {
    path: '/admin/auth/login',
    name: 'login',
    component: Login
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

router.beforeEach(async (to) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isAuthenticated

  if (isAuthenticated && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (e) {
      // Failed to fetch user, probably invalid token
      authStore.logout()
      return '/admin/auth/login'
    }
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    return '/admin/auth/login'
  }

  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    return '/'
  }
})

export default router
