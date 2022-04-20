import React, { useState } from 'react'
import { PricingCard, CardInner, CardTop } from './pricingStyles'
import AddToCartForm from 'Components/AddToCartForm'
import Button from 'Components/primitives/form/Button'

interface Props {
  setPromotionDialog: (item: any) => void
}

const QuarterlyCard = ({ setPromotionDialog }: Props) => {
  const [loading, setLoading] = useState(false)

  return (
    <div className="pricing-card quarterly-card">
      <PricingCard>
        <div className="orange-gradient">
          <CardTop>
            <span className='cardTitle'>Pay Quarterly</span>
            <span className="price">£39.99</span>
            <span className="smallText">3 month&apos;s access</span>
          </CardTop>
          <CardInner>
            <ul>
              <li>Less than £14 a month</li>
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
                    ? 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zMTUzMTk1MDI0MzkwMg=='
                    : 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC80MjQ2MDQzNDY5NDM2Mw=='
                }
              />
              <input type="hidden" name="sku" value="10000076" />
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

export default QuarterlyCard
