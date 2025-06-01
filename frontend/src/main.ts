import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import i18n from '@/shared/services/Translation';
import router from './Routers';

const app = createApp(App)

app.use(createPinia())
app.use(router);
app.use(i18n);

app.mount('#app')
