import React from 'react'
import ContentContainer from 'Components/containers/ContentContainer'
import { MemberPanelContainer } from './styles'

const MemberPanel = (props: any) => {
  const { data } = props

  return (
    <MemberPanelContainer>
      <ContentContainer
        className={`${data.primary.contrast ? 'contrast' : ''} ${
          data.primary.reverse_layout ? 'reverse' : ''
        }`}
      >
        <section className="img">
          <img
            src={data?.primary?.member_image?.url}
            alt={data?.primary?.member_image?.alt}
            loading="lazy"
          />
        </section>
        <section className="content">
          <h3>{data.primary.member_name?.[0].text}</h3>
          <span className="location">
            {data.primary?.member_location?.[0]?.text}
          </span>

          <blockquote>{data.primary.quote?.[0]?.text}</blockquote>
          <p>{data.primary.story?.[0]?.text}</p>
        </section>
      </ContentContainer>
    </MemberPanelContainer>
  )
}

export default MemberPanel
