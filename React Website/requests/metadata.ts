import { StrapiImage } from 'Helpers/strapi'

export interface StrapiMetadata {
  id: number
  title?: string
  description?: string
  keywords: string
  image?: StrapiImage
}
