import React from 'react'
import { RichText } from 'prismic-reactjs'
import { PageHeader } from './styles'

const Index = (props: any) => {
  const { data } = props
  return (
    <>
      <PageHeader>{RichText.render(data?.primary?.title)}</PageHeader>
    </>
  )
}

export default Index
