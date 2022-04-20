import React from 'react'
import { RichText } from 'prismic-reactjs'
import Button from 'Components/primitives/form/Button'
import { VideoContainer } from './styles'

const URLVideoContentPanel = (props: any) => {
  const { data } = props

  return (
    <>
      <VideoContainer
        className={`${data.primary.contrast ? 'contrast' : ''} ${
          data.primary.reverse_layout ? 'reverse' : ''
        }`}
      >
        <section className="content">
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
          <div className="desktop-only">
            {RichText.render(data?.primary?.title)}
          </div>
          {RichText.render(data?.primary?.body1)}

          {data.primary.call_to_action_label?.[0]?.text.length > 0 && (
            <Button href={data.primary.call_to_action?.[0]?.text}>
              {data.primary?.call_to_action_label?.[0]?.text}
            </Button>
          )}
        </section>
        <section className={'video center'}>
          <div className="mobile-only">
            {RichText.render(data?.primary?.title)}
          </div>
          <video
            muted={true}
            autoPlay={true}
            style={{ minHeight: '332px' }}
            src={data.primary.video.url}
            poster="/images/heroposter.png"
            loop
            playsInline
          />
        </section>
      </VideoContainer>
    </>
  )
}

export default URLVideoContentPanel
