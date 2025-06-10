import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import i18n from '@/shared/services/Translation';
import router from './Routers';
import PrimeVue from 'primevue/config';
import Lara from '@primeuix/themes/lara';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';
import Toast from 'primevue/toast';

const app = createApp(App)

app.use(createPinia())
app.use(router);
app.use(i18n);
app.use(PrimeVue, {
  ripple: true,
  theme: {
    preset: Lara,
    options: {
      darkModeSelector: '.dark',
      cssLayer: false
    }
  }
});
app.use(ToastService);
app.use(ConfirmationService);

app.component('Toast', Toast)
app.mount('#app')
