import { StrapiImage as StrapiImageType } from 'Helpers/strapi'
import React, { ImgHTMLAttributes, useMemo } from 'react'

interface Props extends Omit<ImgHTMLAttributes<HTMLImageElement>, 'src' | 'srcSet' | 'alt'> {
  imageData: StrapiImageType['data']
}

const StrapiImage = ({ imageData, ...props }: Props) => {
  if (!imageData) {
    return (
      <img
        src={`${process.env.NEXT_PUBLIC_WEBSITE_URL}/images/not-found.png`}
        alt="Image Missing"
        {...props}
      />
    )
  }

  const { formats, alternativeText } = imageData.attributes

  const imageSourceSet = useMemo(() => {
    const { large, medium, small, thumbnail } = formats ?? {}
    return [large, medium, small, thumbnail]
      .filter(set => !!set)
      .map(({ url, width }) => `${process.env.NEXT_PUBLIC_STRAPI_URL}${url} ${width}w`)
      .join(', ')
  }, [Object.keys(formats || {}).length])

  return (
    <img
      src={`${process.env.NEXT_PUBLIC_STRAPI_URL}${imageData.attributes.url}`}
      srcSet={imageSourceSet}
      alt={alternativeText}
      {...props}
    />
  )
}

export default StrapiImage
