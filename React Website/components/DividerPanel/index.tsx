import React from 'react'
import { RichText } from 'prismic-reactjs'
import CenteredContainer from 'Components/containers/CenteredContainer'
import Button from 'Components/primitives/form/Button'

const DividerPanel = (props: any) => {
  const { data } = props
  return (
    <CenteredContainer contrast={data?.primary?.contrast}>
      {RichText.render(data?.primary?.title)}
      {RichText.render(data?.primary?.body1)}

      {data.primary.label?.[0]?.text.length > 0 && (
        <Button href={data.primary?.call_to_action?.[0]?.text}>
          {data.primary?.label?.[0]?.text}
        </Button>
      )}
    </CenteredContainer>
  )
}

export default DividerPanel
