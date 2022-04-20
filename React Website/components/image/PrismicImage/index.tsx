import React, { ImgHTMLAttributes, useEffect, useState } from 'react'
import { usePrismicImageWindowDimensions } from 'Components/image/PrismicImageProvider'

interface Props extends ImgHTMLAttributes<HTMLImageElement> {
  url: string
  alt: string
  width: number
  height: number
}

const calculateRatio = (innerWidth: number, imageWidth: number, lastRatio?: number) => {
  const boost = 1.5
  let ratio = (innerWidth * boost) / imageWidth

  if (ratio > 1) {
    ratio = 1
  } else if (lastRatio && ratio < lastRatio) {
    ratio = lastRatio
  }

  return ratio
}

const PrismicImage = ({ url, alt, width, height, ...props }: Props) => {
  const { innerWidth = 400 } = usePrismicImageWindowDimensions() ?? {}
  const [ratio, setRatio] = useState(() => calculateRatio(innerWidth, width))

  useEffect(() => setRatio(calculateRatio(innerWidth, width, ratio)), [width, innerWidth])

  const renderWidth = width * ratio
  const renderHeight = height * ratio

  return (
    <img
      src={`${url}${url.includes('?') ? '&' : '?'}w=${renderWidth}&h=${renderHeight}`}
      alt={alt}
      { ...props }
    />
  )
}

export default PrismicImage
