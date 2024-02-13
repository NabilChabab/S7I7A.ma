import { createApp } from 'vue';
import App from './App.vue';
import { createRouter, createWebHistory } from 'vue-router';

import Register from '@/components/Auth/Register.vue';
import Login from '@/components/Auth/Login.vue';
import Dashboard from '@/components/admin/Dashboard.vue';
import Doctor from '@/components/doctor/Doctor-Dashboard.vue';
import Patient from '@/components/patient/Patient.vue';


const routes = [
  { path: '/register', component: Register },
  { path: '/login', component: Login },
  { path: '/admin/dashboard' , component: Dashboard},
  { path: '/doctor/dashboard' , component: Doctor},
  { path: '/home' , component: Patient},
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

createApp(App).use(router).mount('#app');
