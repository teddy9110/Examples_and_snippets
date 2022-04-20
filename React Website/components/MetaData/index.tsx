import { truncate } from 'Helpers/string'
import React from 'react'

interface Props {
  title: string
  description: string
  keywords: string
  thumbnailUrl?: string
  thumbnailAlt?: string
  urlPath?: string
  siteName?: string
}

const MetaData = (props: Props) => {
  const {
    keywords,
    thumbnailUrl = 'images/large-logo.png',
    thumbnailAlt = 'Team RH Fitness Logo',
    urlPath = '',
  } = props

  const title = truncate(`Team RH | ${props.title}`)
  const description = truncate(props.description)
  const siteName = truncate(props.siteName ?? 'Team RH Fitness')

  return (
    <>
      <meta name="description" content={description} />
      <meta name="keywords" content={keywords} />

      <meta property="og:title" content={title} />
      <meta property="og:description" content={description} />
      <meta property="og:image" content={thumbnailUrl} />
      <meta property="og:url" content={`${process.env.NEXT_PUBLIC_WEBSITE_URL}/${urlPath}`} />
      <meta property="og:site_name" content={siteName} />

      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:image:alt" content={thumbnailAlt} />

      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: `{
            "@context": "http://schema.org",
            "@type": "LocalBusiness",
            "name": "${title}",
            "description": "${description}"
          }`,
        }}
      />
    </>
  )
}

export default MetaData
