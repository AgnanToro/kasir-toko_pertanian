import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: () => import('@/Pages/LoginView.vue'),
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
          component: () => import('@/Pages/DashboardView.vue')
        },
        {
          path: 'pos',
          name: 'pos',
          component: () => import('@/Pages/PosView.vue')
        },
        {
          path: 'products',
          name: 'products',
          component: () => import('@/Pages/ProductListView.vue')
        },
        {
          path: 'products/create',
          name: 'product-create',
          component: () => import('@/Pages/ProductFormView.vue')
        },
        {
          path: 'products/:id',
          name: 'product-detail',
          component: () => import('@/Pages/ProductDetailView.vue')
        },
        {
          path: 'products/:id/edit',
          name: 'product-edit',
          component: () => import('@/Pages/ProductFormView.vue')
        },
        {
          path: 'categories',
          name: 'categories',
          component: () => import('@/Pages/CategoryView.vue')
        },
        {
          path: 'transactions',
          name: 'transactions',
          component: () => import('@/Pages/TransactionListView.vue')
        },
        {
          path: 'transactions/:id',
          name: 'transaction-detail',
          component: () => import('@/Pages/TransactionDetailView.vue')
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // If route doesn't exist, redirect to login
  if (!to.matched.length) {
    next('/login')
    return
  }
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

// Error handler
router.onError((error) => {
  console.error('Router error:', error)
})

export default router
