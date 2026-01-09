import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    component: () => import('@/layouts/UserLayout.vue'),
    children: [
      {
        path: '',
        name: 'Home',
        component: () => import('@/views/user/Home.vue')
      },
      {
        path: 'lookbook',
        name: 'Lookbook',
        component: () => import('@/views/user/Lookbook.vue')
      },
      {
        path: 'about',
        name: 'About',
        component: () => import('@/views/user/About.vue')
      },
      {
        path: 'contact',
        name: 'Contact',
        component: () => import('@/views/user/Contact.vue')
      }
    ]
  },
  {
    path: '/fanggangrong/login',
    name: 'AdminLogin',
    component: () => import('@/views/admin/Login.vue')
  },
  {
    path: '/fanggangrong',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        redirect: '/fanggangrong/categories'
      },
      {
        path: 'categories',
        name: 'AdminCategories',
        component: () => import('@/views/admin/Categories.vue')
      },
      {
        path: 'sub-categories',
        name: 'AdminSubCategories',
        component: () => import('@/views/admin/SubCategories.vue')
      },
      {
        path: 'articles',
        name: 'AdminArticles',
        component: () => import('@/views/admin/Articles.vue')
      },
      {
        path: 'contact-forms',
        name: 'AdminContactForms',
        component: () => import('@/views/admin/ContactForms.vue')
      },
      {
        path: 'newsletter-subscriptions',
        name: 'AdminNewsletterSubscriptions',
        component: () => import('@/views/admin/NewsletterSubscriptions.vue')
      },
      {
        path: 'products',
        name: 'AdminProducts',
        component: () => import('@/views/admin/Products.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// 路由守卫
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('admin_token')
  
  if (to.meta.requiresAuth && !token) {
    next('/fanggangrong/login')
  } else if (to.path === '/fanggangrong/login' && token) {
    next('/fanggangrong')
  } else {
    next()
  }
})

export default router

