import React, { useState } from 'react'
import Dialog from 'Components/Dialog'
import Button from 'Components/primitives/form/Button'
import { RichText } from 'prismic-reactjs'
import { Banner } from './styles'

const StickyBanner = (props: any) => {
  const { data } = props
  const [dialogStatus, setDialogStatus] = useState(false)

  return (
    <Banner>
      <Button onClick={() => setDialogStatus(!dialogStatus)}>
        {data.primary.label[0].text}
      </Button>
      <Dialog
        control={setDialogStatus}
        open={dialogStatus}
        title={data.primary.banner_title?.[0]?.text || 'Modal'}
      >
        <div style={{ padding: '1rem' }}>
          {RichText.render(data.primary.body1)}
          <div
            dangerouslySetInnerHTML={{
              __html: data.primary.embed_code[0].text,
            }}
          />
        </div>
      </Dialog>
    </Banner>
  )
}

export default StickyBanner
