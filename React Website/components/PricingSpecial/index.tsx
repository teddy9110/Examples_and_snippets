import React, { useState } from 'react'
import { PricingContainer, PricingPanel, SliceTitle } from 'Components/Pricing/styles'
import Dialog from 'Components/Dialog'
import DesktopPricingSpecialCard from './DesktopPricingSpecialCard'
import MobilePricingSpecialCard from './MobilePricingSpecialCard'
import PromotedDialog from 'Components/PromotedDialog'

const Pricing = (props) => {
  const [videoModal, showVideoModal] = useState(false)
  const [promotionDialog, setPromotionDialog] = useState({
    title: null,
    description: '',
    open: false,
  })

  return (
    <>
      <PricingPanel contrast={true}>
        <section>
          <PricingContainer>
            <SliceTitle>
              <h3>One Plan, Two Payment Options</h3>
            </SliceTitle>
            <div>
              <section className="large-view">
                <DesktopPricingSpecialCard setPromotionDialog={setPromotionDialog} />
              </section>
              <section className="small-view">
                <MobilePricingSpecialCard setPromotionDialog={setPromotionDialog} />
              </section>
            </div>
          </PricingContainer>
        </section>
      </PricingPanel>

      <Dialog
        control={() => showVideoModal(false)}
        open={videoModal}
        title={'Tour Of The App'}
      >
        <p>
          <iframe
            width="100%"
            height="315"
            src="https://www.youtube.com/embed/yLDlGCxJaxw"
            title="YouTube video player"
            frameBorder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowFullScreen
          ></iframe>
        </p>
      </Dialog>

      <PromotedDialog
        open={promotionDialog.open}
        title={promotionDialog.title}
        data={promotionDialog}
        control={() => {
          setPromotionDialog({ ...promotionDialog, open: false })
          location.replace('/store/cart')
        }}
      />
    </>
  )
}

export default Pricing
