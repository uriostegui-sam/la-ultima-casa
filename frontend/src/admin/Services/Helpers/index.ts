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