import { createI18n } from 'vue-i18n';
import { ref } from 'vue';
import es from '@/Locales/es.json';
import en from '@/Locales/en.json';
import { getFromStorage, setToStorage } from '@/Services/Helpers/LocalStorage';

export enum Languages {
  Spanish = 'es-ES',
  English = 'en-GB',
}

export type AvailableLanguagesInterface = `${Languages}`;

const userLang = navigator.language;

if (getFromStorage('locale') === null) {
  const defaultLang = userLang.startsWith('es') ? Languages.Spanish : Languages.English;
  setToStorage('locale', defaultLang);
}

const locale = ref(
  (getFromStorage('locale') as AvailableLanguagesInterface) || Languages.Spanish
);

const i18n = createI18n({
  legacy: false,
  allowComposition: true,
  globalInjection: true,
  locale: locale.value,
  fallbackLocale: Languages.Spanish,
  messages: {
    [Languages.Spanish]: es,
    [Languages.English]: en,
  },
});

export function switchLanguage(newLocale: AvailableLanguagesInterface) {
  locale.value = newLocale;
  i18n.global.locale.value = newLocale;
  setToStorage('locale', newLocale);
}

export default i18n;
export { locale };
