import React from 'react'
import Flickety, { FlickityOptions } from 'react-flickity-component'
import 'flickity/css/flickity.css'
import MonthlySpecialCard from './MonthlySpecialCard'
import AnnualSpecialCard from './AnnualCard'

interface Props {
  setPromotionDialog: (item: any) => void
}

const MobilePricingSpecialCard = ({ setPromotionDialog }: Props) => {
  const flickityOptions: FlickityOptions = {
    initialIndex: 0,
    wrapAround: false,
    friction: 0.87,
    selectedAttraction: 0.15,
    prevNextButtons: false,
    pageDots: true,
    cellAlign: 'left',
    accessibility: true,
    dragThreshold: 60,
    contain: true,
  }

  return (
    <>
      <Flickety
        className="carousel"
        elementType="div"
        options={flickityOptions}
        reloadOnUpdate
        static={true}
      >
        <MonthlySpecialCard setPromotionDialog={setPromotionDialog} />
        <AnnualSpecialCard setPromotionDialog={setPromotionDialog} />
      </Flickety>
    </>
  )
}

export default MobilePricingSpecialCard
