import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import MainLayout from '../layouts/MainLayout.vue'
import Dashboard from '../views/Dashboard.vue'
import Rooms from '../views/Rooms.vue'
import Tenants from '../views/Tenants.vue'
import Contracts from '../views/Contracts.vue'
import Payments from '../views/Payments.vue'
import Maintenance from '../views/Maintenance.vue'
import { auth } from '../firebase/config'
import { onAuthStateChanged } from 'firebase/auth'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/dashboard'
      },
      {
        path: 'dashboard',
        name: 'Dashboard',
        component: Dashboard
      },
      {
        path: 'rooms',
        name: 'Rooms',
        component: Rooms
      },
      {
        path: 'tenants',
        name: 'Tenants',
        component: Tenants
      },
      {
        path: 'contracts',
        name: 'Contracts',
        component: Contracts
      },
      {
        path: 'payments',
        name: 'Payments',
        component: Payments
      },
      {
        path: 'maintenance',
        name: 'Maintenance',
        component: Maintenance
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  const requiresGuest = to.matched.some(record => record.meta.requiresGuest)

  // Check if user is authenticated
  const unsubscribe = onAuthStateChanged(auth, (user) => {
    if (requiresAuth && !user) {
      // Route requires auth but user is not authenticated
      next('/login')
    } else if (requiresGuest && user) {
      // Route requires guest but user is authenticated
      next('/dashboard')
    } else {
      // No special requirements or requirements are met
      next()
    }
    
    // Unsubscribe to avoid memory leaks
    unsubscribe()
  })
})

export default router 