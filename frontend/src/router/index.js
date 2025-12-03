import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/',
      component: () => import('@/layouts/MainLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('@/views/DashboardView.vue')
        },
        {
          path: 'pos',
          name: 'pos',
          component: () => import('@/views/PosView.vue')
        },
        {
          path: 'products',
          name: 'products',
          component: () => import('@/views/ProductListView.vue')
        },
        {
          path: 'products/create',
          name: 'product-create',
          component: () => import('@/views/ProductFormView.vue')
        },
        {
          path: 'products/:id/edit',
          name: 'product-edit',
          component: () => import('@/views/ProductFormView.vue')
        },
        {
          path: 'categories',
          name: 'categories',
          component: () => import('@/views/CategoryView.vue')
        },
        {
          path: 'transactions',
          name: 'transactions',
          component: () => import('@/views/TransactionListView.vue')
        },
        {
          path: 'transactions/:id',
          name: 'transaction-detail',
          component: () => import('@/views/TransactionDetailView.vue')
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
