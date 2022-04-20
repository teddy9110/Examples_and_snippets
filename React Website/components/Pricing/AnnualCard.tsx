import React, { useState } from 'react'
import { PricingCard, CardInner, CardTop } from './pricingStyles'
import AddToCartForm from 'Components/AddToCartForm'
import Button from 'Components/primitives/form/Button'

interface Props {
  setPromotionDialog: (item: any) => void
}

const AnnualCard = ({ setPromotionDialog }: Props) => {
  const [loading, setLoading] = useState(false)

  return (
    <div className="pricing-card annual-card">
      <PricingCard>
        <div className="orange-gradient">
          <CardTop>
            <span className='cardTitle'>Pay Annually</span>
            <span className="price">£59.99</span>
          </CardTop>
          <CardInner>
            <ul>
              <li>12 Month Contract</li>
              <li>Includes all features</li>
              <li>No sign-up fee</li>
            </ul>

            <AddToCartForm
              setLoading={setLoading}
              setPromotionDialog={setPromotionDialog}
            >
              <input
                type="hidden"
                name="variantId"
                value={
                  process.env.NEXT_PUBLIC_ENV === 'production'
                    ? 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTUxNTUxMjI3NTAwNg=='
                    : 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC80MjA3OTQyNzQ2MTMzOQ=='
                }
              />
              <input type="hidden" name="sku" value="10000001" />
              <input type="hidden" name="quantity" value={1} />

              <Button loading={loading} type="submit" className='signUp'>
                Buy Now
              </Button>
            </AddToCartForm>
          </CardInner>
        </div>
      </PricingCard>
    </div>
  )
}

export default AnnualCard
