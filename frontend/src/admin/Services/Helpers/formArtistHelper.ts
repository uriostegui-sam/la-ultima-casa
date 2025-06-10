import type { ArtistCreatePayload, ArtistUpdatePayload } from "@/shared/Interfaces/Artist";

export function buildArtistFormData(
  payload: ArtistCreatePayload | ArtistUpdatePayload
): FormData {
  const formData = new FormData();

  // User fields
  if ('user_id' in payload && payload.user_id) {
    formData.append('user_id', payload.user_id.toString());
  }
  if (payload.user?.email) {
    formData.append('user[email]', payload.user.email.toString());
  }
  if (payload.user?.name) {
    formData.append('user[name]', payload.user.name.toString());
  }
  if (payload.user?.lastname) {
    formData.append('user[lastname]', payload.user.lastname.toString());
  }

  // Content fields
  if (payload.minibio?.en) {
    formData.append('minibio[en]', payload.minibio.en.toString());
  }
  if (payload.minibio?.es) {
    formData.append('minibio[es]', payload.minibio.es.toString());
  }
  if (payload.bio?.es) {
    formData.append('bio[es]', payload.bio.es.toString());
  }
  if (payload.bio?.en) {
    formData.append('bio[en]', payload.bio.en.toString());
  }

  // Social links
  if (payload.social_links?.website) {
    formData.append('social_links[website]', payload.social_links.website.toString());
  }
  if (payload.social_links?.instagram) {
    formData.append('social_links[instagram]', payload.social_links.instagram.toString());
  }
  if (payload.social_links?.twitter) {
    formData.append('social_links[twitter]', payload.social_links.twitter.toString());
  }
  if (payload.social_links?.flickr) {
    formData.append('social_links[flickr]', payload.social_links.flickr.toString());
  }

  // File uploads
  if ('profile_image' in payload && payload.profile_image) {
    formData.append('profile_image', payload.profile_image);
  }

  // Skills
  if ('skills' in payload && payload.skills) {
    payload.skills.forEach(id => formData.append('skills[]', id.toString()));
  }

  return formData;
}