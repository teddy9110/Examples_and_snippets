/* eslint-disable no-fallthrough */
import { StrapiImage, StrapiImageFormatKeys } from '.'

type Sizes = StrapiImageFormatKeys | 'original'
type Formats = StrapiImage['data']['attributes']['formats']

const getLargestImageFromFormat = (formats: Formats, url: string, format: Sizes) => {
  switch (format) {
    case 'large':
      if (formats.large?.url) {
        return formats.large?.url
      }

    case 'medium':
      if (formats.medium?.url) {
        return formats.medium?.url
      }

    case 'small':
      if (formats.small?.url) {
        return formats.small?.url
      }

    case 'thumbnail':
      if (formats.thumbnail?.url) {
        return formats.thumbnail?.url
      }

    default:
      return url
  }
}

export const getLargestImageUrl = (imageData: StrapiImage['data'], format: Sizes = 'large') => {
  if (!imageData) {
    return `${process.env.NEXT_PUBLIC_WEBSITE_URL}/images/not-found.png`
  }

  const { attributes: image } = imageData

  if (!image.formats) {
    return `${process.env.NEXT_PUBLIC_STRAPI_URL}${image.url}`
  }

  return `${process.env.NEXT_PUBLIC_STRAPI_URL}${getLargestImageFromFormat(image.formats, image.url, format)}`
}
