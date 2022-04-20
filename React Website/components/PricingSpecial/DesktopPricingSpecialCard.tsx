import React from 'react'
import AnnualSpecialCard from './AnnualCard'
import MonthlySpecialCard from './MonthlySpecialCard'
import { PricingCardContainer } from './PricingSpecialStyles'

interface Props {
  setPromotionDialog: (item: any) => void
}

const DesktopPricingSpecialCard = ({ setPromotionDialog }: Props) => {
  return (
    <>
      <PricingCardContainer>
        <MonthlySpecialCard setPromotionDialog={setPromotionDialog} />
        <AnnualSpecialCard setPromotionDialog={setPromotionDialog} />
      </PricingCardContainer>
    </>
  )
}

export default DesktopPricingSpecialCard
