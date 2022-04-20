import React from 'react'
import { useRouter } from 'next/router'
import { ExitButton } from './styles'

const ExitPreviewButton = ({ children }: any) => {
  const { isPreview } = useRouter()
  return (
    <div>
      {children}
      {isPreview
        ? (
          <a className="exit-button" href="/api/exit-preview">Exit Preview</a>
        )
        : null}
      <ExitButton />
    </div>
  )
}

export default ExitPreviewButton
