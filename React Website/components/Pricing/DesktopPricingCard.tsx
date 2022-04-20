import React from 'react'
import AnnualCard from './AnnualCard'
import MonthlyCard from './MonthlyCard'
import { PricingCardContainer } from './pricingStyles'

interface Props {
  setPromotionDialog: (item: any) => void
}

const DesktopPricingCard = ({ setPromotionDialog }: Props) => {
  return (
    <>
      <PricingCardContainer>
        <MonthlyCard setPromotionDialog={setPromotionDialog} />
        <AnnualCard setPromotionDialog={setPromotionDialog} />
      </PricingCardContainer>
    </>
  )
}

export default DesktopPricingCard
