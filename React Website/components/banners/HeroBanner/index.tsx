import React from 'react'
import { HeroBannerBase } from './styles'

const HeroBanner = (props: any) => {
  const { children } = props
  return <HeroBannerBase {...props}>{children}</HeroBannerBase>
}

export default HeroBanner
