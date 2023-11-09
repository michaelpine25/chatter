import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './Pages/Dashboard.vue'; 
import ChatBox from './Components/ChatBox.vue';
import Welcome from './Pages/Welcome.vue';
import Profile from './Pages/Profile/Edit.vue';
import Login from './Pages/Auth/Login.vue';
import Register from './Pages/Auth/Register.vue';

const routes = [
  { path: '/', name: 'welcome', component: Welcome },
  { path: '/dashboard', name: 'dashboard', component: Dashboard },
  { path: '/conversation/:id', name: 'conversation', component: ChatBox, props: true },
  { path: '/profile', name: 'profile', component: Profile},
  { path: '/login', name: 'login', component: Login},
  { path: '/register', name: 'register', component: Register},
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

export default router;
