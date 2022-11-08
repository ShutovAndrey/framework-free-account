import Vue from 'vue'
import VueRouter from 'vue-router'
import Cookies from 'js-cookie'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    redirect: { name: 'panel' }
  },
  {
    path: '/panel',
    name: 'panel',
    component: () => import('@/views/Cabinet'),
    meta: {
      title: 'User Panel'
    }
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login'),
    meta: {
      title: 'Login'
    }
  }
]

const router = new VueRouter({
  mode: 'history',
  routes
})

router.beforeEach((to, from, next) => {
  if (to.name !== 'login' && !Cookies.get('token')) next({ name: 'login' })
  next()
})

export default router
