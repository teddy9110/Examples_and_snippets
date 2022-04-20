import React from 'react'
import { ComparisonImage } from './styles'

interface Props {
  data: any
}

const ComparisonsMobile = ({ data }: Props) => {
  return (
    <ComparisonImage
      src="/images/latest-comparison-table.png"
      alt="Price Comparison Image"
      loading="lazy"
    />
  )
}

export default ComparisonsMobile
