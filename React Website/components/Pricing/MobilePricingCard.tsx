import React from 'react'
import MonthlyCard from './MonthlyCard'
import Flickety, { FlickityOptions } from 'react-flickity-component'
import AnnualCard from './AnnualCard'
import 'flickity/css/flickity.css'

interface Props {
  setPromotionDialog: (item: any) => void
}

const MobilePricingCard = ({ setPromotionDialog }: Props) => {
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
      <div>
        <Flickety
          className="carousel"
          elementType="div"
          options={flickityOptions}
          reloadOnUpdate
          static={true}
        >
          <MonthlyCard setPromotionDialog={setPromotionDialog} />
          <AnnualCard setPromotionDialog={setPromotionDialog} />
        </Flickety>
      </div>
    </>
  )
}

export default MobilePricingCard
