import React from 'react'
import ContentContainer from 'Components/containers/ContentContainer'
import { RichText } from 'prismic-reactjs'
import Button from 'Components/primitives/form/Button'

const VideoContentPanel = (props: any) => {
  const { data } = props

  return (
    <>
      <ContentContainer
        className={`${data.primary.contrast ? 'contrast' : ''} ${
          data.primary.reverse_layout ? 'reverse' : ''
        }`}
      >
        <section>
          {data?.primary?.icon.url !== undefined
            ? (
              <img
                className="icon"
                loading="lazy"
                src={data?.primary?.icon?.url}
                alt={data?.primary?.icon?.alt || ' '}
                {...data?.primary?.icon?.dimensions}
              />
            )
            : null}
          {RichText.render(data?.primary?.title)}
          {RichText.render(data?.primary?.body1)}

          {data.primary.call_to_action_label?.[0]?.text.length > 0 && (
            <Button href={data.primary.call_to_action?.[0]?.text}>
              {data.primary?.call_to_action_label?.[0]?.text}
            </Button>
          )}
        </section>
        <section className={'embed center'}>
          {RichText.render(data?.primary?.embed_code)}
        </section>
      </ContentContainer>
    </>
  )
}

export default VideoContentPanel
