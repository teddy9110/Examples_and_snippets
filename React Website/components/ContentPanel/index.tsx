import React from 'react'
import ContentContainer from 'Components/containers/ContentContainer'
import { RichText } from 'prismic-reactjs'
import Button from 'Components/primitives/form/Button'

const ContentPanel = (props: any) => {
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
                loading="lazy"
                className="icon"
                src={data?.primary?.icon?.url}
                alt={data?.primary?.icon?.alt || ' '}
                style={{ height: data?.primary?.icon.dimensions.height }}
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
        <section className={`img ${data?.primary?.image_position}`}>
          <picture>
            <source
              srcSet={data?.primary?.body_image?.mobile?.url}
              media="(max-width: 901px)"
            />
            <img
              loading="lazy"
              src={data?.primary?.body_image?.url}
              alt={data?.primary?.body_image?.alt || ' '}
              width="600px"
              height="600px"
            />
          </picture>
        </section>
      </ContentContainer>
    </>
  )
}

export default ContentPanel
