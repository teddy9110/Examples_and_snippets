import cheerio, { Element } from 'cheerio'
import atob from 'atob'

export interface StrapiImageFormat {
  name: string
  hash: string
  ext: string
  mime: string
  width: number
  height: number
  size: number
  path?: string
  url: string
}

export interface StrapiImage {
  id: number
  name: string
  alternativeText: string
  caption: string
  width: number
  height: number
  formats: {
    thumbnail?: StrapiImageFormat
    large?: StrapiImageFormat
    medium?: StrapiImageFormat
    small?: StrapiImageFormat
  }
  hash: string
  ext: string
  mime: string
  size: number
  url: string
  previewUrl?: string
  provider: string
  provider_metadata?: any
  createdAt: string
  updatedAt: string
}

export const transformRichText = (content: string) => {
  if (!content) {
    return ''
  }

  const root = cheerio.load(content)

  root('img').replaceWith((_index, element: Element) => {
    const {
      src,
      alt,
      width,
      'data-strapi-image': imageData,
      'data-align': align,
      'data-link': link,
    } = element.attribs
    let imageSourceSet: string

    if (imageData) {
      const strapiImage: StrapiImage = JSON.parse(atob(imageData))

      if (strapiImage.formats) {
        const { large, medium, small, thumbnail } = strapiImage.formats

        imageSourceSet = [large, medium, small, thumbnail]
          .filter(set => !!set)
          .map(({ url, width }, index) => `${process.env.NEXT_PUBLIC_STRAPI_URL}${url} ${width}w`)
          .join(', ')
      }
    }

    let imageStyles = `width: 100%; max-width: ${width}px; height: auto; display: block !important`

    if (align) {
      let margin = '0 auto 0 0'

      if (align === 'left') {
        margin = '0 auto 0 0'
      } else if (align === 'center') {
        margin = '0 auto 0 auto'
      } else if (align === 'right') {
        margin = '0 0 0 auto'
      }

      imageStyles = `${imageStyles}; margin: ${margin}`
    }

    let image = `
      <img
        src="${src}"
        ${imageSourceSet ? `srcset="${imageSourceSet}"` : ''}
        alt="${alt}"
        style="${imageStyles}"
      />
    `

    if (link) {
      image = `<a href="${link}" rel="noopener noreferrer" target="_blank">${image}</a>`
    }

    return `<p>${image}</p>`
  })

  root('iframe').each(function () {
    const element = root(this)
    const width = parseInt(element.attr('width'))
    element.removeAttr('width')
    element.removeAttr('height')

    element.css('width', `${width}px`)
    element.css('max-width', '100%')
    element.css('aspect-ratio', '16/9')
  })

  return root.html()
}
