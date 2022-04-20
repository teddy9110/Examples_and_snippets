import React, { useState } from 'react'
import Button from 'Components/primitives/form/Button'
import AddToCartForm from 'Components/AddToCartForm'
import btoa from 'btoa'
import { Wrapper } from './styles'
import PriceComparison from 'Components/PriceComparison'
import { DateTime } from 'luxon'
import getProductIdFromVariant from './get-product-id-from-variant'

interface Props {
  handle: string
  title: string
  variants: any[]
  images: any[]
  availableForSale: boolean
  tags?: any[]
  isPreorder?: boolean
  availableOn?: string
  key: any
  stagingId?: string
  setPromotionDialog?: (data: any) => void
}

const ProductCard = (props: Props) => {
  const {
    variants,
    images,
    title,
    handle,
    availableForSale,
    isPreorder,
    availableOn,
    stagingId,
    setPromotionDialog = () => {},
  } = props

  const [loading, setLoading] = useState(false)

  return (
    <Wrapper>
      <a href={`/store/${handle}`}>
        <div className="thumb">
          <img src={images[0].src} alt={images[0].altText} loading="lazy" />
        </div>
        <header>
          <h3>{title}</h3>
          <PriceComparison
            variant={variants[0]}
          />
          <div className="available-on">
            {isPreorder && `Available on ${DateTime.fromISO(availableOn).toLocaleString(DateTime.DATE_MED)}`}
            {!isPreorder && (availableForSale ? 'In Stock' : 'Sold Out')}
          </div>
        </header>
      </a>
      <AddToCartForm
        setLoading={setLoading}
        setPromotionDialog={setPromotionDialog}
      >
        {(variants || []).length === 1
          ? (
            <>
              <input
                type="hidden"
                name="variantId"
                value={btoa(getProductIdFromVariant(variants[0], stagingId))}
              />
              <input type="hidden" name="sku" value={variants[0].sku} />
              <input min="1" type="hidden" name="quantity" defaultValue={1} />
              {availableForSale
                ? (
                  <Button
                    loading={loading}
                    style={{ width: '100%', maxWidth: '100%' }}
                    type="submit"
                  >
                    <i className="fa fa-shopping-cart" aria-hidden="true"></i>
                    {isPreorder ? 'Pre-order' : 'Add'}
                  </Button>
                )
                : (
                  'Sold Out'
                )}
            </>
          )
          : (
            <Button href={`/store/${handle}`}>View Product</Button>
          )}
      </AddToCartForm>
    </Wrapper>
  )
}

export default ProductCard
