import React, { useMemo } from 'react'
import { ReducedPrice, PriceDiffrence } from './styles'

interface Variant {
  variant: {
    avaliable: boolean
    compareAtPrice: string
    id: string
    price: string
  }
}

const PriceComparison: React.FC<Variant> = (props) => {
  const { variant } = props

  const isThresholdAbove = useMemo(() => {
    if (variant.compareAtPrice == null) {
      return false
    }

    let price: number = parseFloat(variant.compareAtPrice) - parseFloat(variant.price)
    price = parseFloat(price.toFixed(2))

    return price >= parseFloat(process.env.NEXT_PUBLIC_PRICE_REDUCTION_THRESHOLD ?? '0')
  }, [variant.compareAtPrice, variant.price])

  return (
    <>
      {isThresholdAbove
        ? <ReducedPrice> <del>£{variant.compareAtPrice}</del></ReducedPrice>
        : null
      }
      <span>£{variant.price}</span>
      {isThresholdAbove
        ? <PriceDiffrence>
          save £ {(parseFloat(variant.compareAtPrice) - parseFloat(variant.price)).toFixed(2)}
        </PriceDiffrence>
        : null
      }
    </>
  )
}

export default PriceComparison
