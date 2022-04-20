import React, { useState } from 'react'
import { PricingCard, CardInner, CardTop, CardHeader } from './PricingSpecialStyles'
import Dialog from 'Components/Dialog'
import AddToCartForm from 'Components/AddToCartForm'
import Button from 'Components/primitives/form/Button'

interface Props {
  setPromotionDialog: (item: any) => void
}

const MonthlySpecialCard = ({ setPromotionDialog }: Props) => {
  const [twelveMonth, setTwelveMonth] = useState(false)
  const [loading, setLoading] = useState(false)

  return (
    <div className="pricing-card monthly-card">
      <PricingCard>
        <CardHeader>
          <i className='question-white fa fa-question-circle' onClick={() => setTwelveMonth(true)}></i>
          <div className='savings'>
            <span>special offer </span>
            <span>save £20</span>
          </div>
        </CardHeader>
        <div className="blue-gradient">
          <CardTop>
            <span className='cardTitle'>Pay Monthly</span>
            <span className="price">£6.99</span>
          </CardTop>
          <CardInner>
            <ul>
              <li>12 month access</li>
              <li>Includes all features</li>
              <li>£20 sign-up fee waived!</li>
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
                    ? 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC8zOTcwNzI2Mjc0NjY4Ng=='
                    : 'Z2lkOi8vc2hvcGlmeS9Qcm9kdWN0VmFyaWFudC80MjUwNDAxODQyODEyMw=='
                }
              />
              <input type="hidden" name="sku" value="10000082" />
              <input type="hidden" name="quantity" value={1} />

              <Button loading={loading} type="submit" className='signUp'>
                Buy Now
              </Button>
            </AddToCartForm>
          </CardInner>
        </div>
      </PricingCard>

      <Dialog
        control={() => setTwelveMonth(false)}
        open={twelveMonth}
        title={'Why do I have to sign up to a 12 month contract?'}
      >
        <p>
          {`
            Now, when myself and Rachael first started this business we made a
            promise to ourselves that we wanted to change people's lives no matter
            what their circumstances were. For that reason we decided to offer our
            Life Plan for the cheapest price we possibly could whilst still being
            able to keep the business going. We charge £6.99 per month compared to
            our competitors who are charging £40+ per month for their standard
            plans, and that's before any of the 'premium' features they make you
            cough up for. We give you personalised coaching, personalised calorie
            and macro goals plus workouts and thousands of recipes, community
            support, step targets and an entire database of information designed
            to help you completely change your life, body and mindset.
          `}
        </p>
        <p>
          {`
            Now we couldn't offer all that for just £6.99 without having some
            assurance that you're in it for the long haul, otherwise you could get
            your calorie and macros targets and then cancel, trying to follow the
            plan without any of the guidance and nutritional education that you
            really need to get results.
          `}
        </p>
        <p>
          {`
            We also want our members to understand this is not a quick fix - it's
            a change for life, not a crash diet. That's why we're asking you to
            put your trust in us. Give us one year, and we'll help you
          `}
          <b>change your life for good.</b>
        </p>
      </Dialog>
    </div>
  )
}

export default MonthlySpecialCard
