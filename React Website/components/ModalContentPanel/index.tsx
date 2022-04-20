import React, { useState } from 'react'
import { ResponsiveControl } from './styles'
import Dialog from 'Components/Dialog'
import Button from 'Components/primitives/form/Button'
import { RichText } from 'prismic-reactjs'
import ContentContainer from 'Components/containers/ContentContainer'

const ContentPanel = (props: any) => {
  const { data } = props
  const [dialogStatus, setDialogStatus] = useState(false)

  return (
    <ResponsiveControl>
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
                {...data?.primary?.icon?.dimensions}
              />
            )
            : null}
          {RichText.render(data?.primary?.title)}
          {RichText.render(data?.primary?.body1)}
          <div className="desktop-only">
            {data.primary.label[0].text.length > 0 && (
              <Button
                className="desktop-only"
                onClick={() => setDialogStatus(!dialogStatus)}
              >
                {data.primary.label[0].text}
              </Button>
            )}
          </div>
        </section>

        <section className={'img centers'}>
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
          <div className="mobile-only">
            <Button onClick={() => setDialogStatus(!dialogStatus)}>
              {data.primary.label[0].text}
            </Button>
          </div>
        </section>

        <Dialog
          control={setDialogStatus}
          open={dialogStatus}
          title={data.primary.banner_title?.[0]?.text || 'Modal'}
        >
          <div style={{ padding: '1rem' }}>
            {RichText.render(data.primary.modal_body)}
            <div
              dangerouslySetInnerHTML={{
                __html: data.primary.embed_code[0].text,
              }}
            />
          </div>
        </Dialog>
      </ContentContainer>
    </ResponsiveControl>
  )
}

export default ContentPanel
