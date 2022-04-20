import React from 'react'
import ComparisonsDesktop from 'Components/comparisons/ComparisonsDesktop'
import ComparisonsMobile from 'Components/comparisons/ComparisonsMobile'
import { ComparisonsContainer, ComparisonsContent } from './styles'

interface Props {
  data: any
}

const Comparisons = ({ data }: Props) => {
  return (
    <ComparisonsContainer>
      <h3>How Do We Compare To Other Plans?</h3>
      <ComparisonsContent>
        <section className="large-view">
          <ComparisonsDesktop data={data} />
        </section>
        <section className="small-view">
          <ComparisonsMobile data={data} />
        </section>
        <span className="disclaimer">*All infomation correct at time of publication.</span>
      </ComparisonsContent>
    </ComparisonsContainer>
  )
}

export default Comparisons
