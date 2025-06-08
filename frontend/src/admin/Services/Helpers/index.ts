import { capitalizeFirstLetter } from "@/shared/services/Helpers";

export const getLanguageStatus = (textObj: { en?: string; es?: string }) => {
  const hasEN = !!textObj?.en;
  const hasES = !!textObj?.es;
  
  if (hasEN && hasES) return '✅'; 
  if (hasES) return '🇲🇽';
  if (hasEN) return '🇺🇸';
  return '❌';
};

export const getSocialLinksStatus = (links: Object) => {
  const completed = Object.entries(links)
    .filter(([_, value]) => value !== null)
    .map(([key]) => key);
  
  return completed.length === 4 
    ? 'pi-face-smile' 
    : completed.map(platform => {
        switch(platform) {
          case 'instagram': return 'pi-instagram';
          case 'twitter': return 'pi-twitter';
          case 'website': return 'pi-globe';
          case 'flickr': return 'pi-tiktok';
          default: return '';
        }
      }).join(' ');
};

export const showSuccessToast = (toast: any, t: Function, key: string, life = 3000) => {
  toast.add({
    severity: 'success',
    summary: capitalizeFirstLetter(t('success')),
    detail: capitalizeFirstLetter(t(key)),
    life: life,
  });
}

export const showErrorToast = (toast: any, t: Function, err: unknown, fallbackKey: string, life = 5000) => {
  let errorMessage = t(fallbackKey);

  if (err && typeof err === 'object' && 'message' in err) {
    errorMessage = (err as { message?: string })?.message ?? errorMessage;
  }

  toast.add({
    severity: 'error',
    summary: capitalizeFirstLetter(t('error')),
    detail: capitalizeFirstLetter(t(errorMessage)) || capitalizeFirstLetter(t(fallbackKey)),
    life,
  });
}
