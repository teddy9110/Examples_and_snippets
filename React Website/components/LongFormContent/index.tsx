import React from 'react'
import { Body } from './styles'
import { RichText } from 'prismic-reactjs'

const Index = (props: any) => {
  const { data } = props
  return (
    <>
      <Body>{RichText.render(data?.primary?.body1)}</Body>
    </>
  )
}

export default Index
