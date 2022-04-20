import React from 'react'
import { RichTextContent } from './styles'
import { useRichText } from './useRichText'

interface Props {
  children: string
}

const RichText = ({ children }: Props) => {
  const content = useRichText(children)

  return (
    <RichTextContent
      dangerouslySetInnerHTML={{ __html: content }}
    />
  )
}

export default RichText
