import React from 'react'

/**
 * @deprecated Replace with MetaData
 */
const Metahead = ({
  description = '',
  keywords = '',
  sitename = '',
  title = '',
  url = '',
  category = 'page',
  pubDate = '',
  imageUrl = '',
  imageAlt = '',
}: any) => (
  <>
    <meta name="description" content={description} />
    <meta name="keywords" content={keywords} />

    <meta property="og:title" content={title} />
    <meta property="og:description" content={description} />
    <meta property="og:image" content={imageUrl} />
    <meta property="og:url" content={url} />
    <meta name="twitter:card" content="summary_large_image" />

    <meta property="og:site_name" content={sitename} />
    <meta name="twitter:image:alt" content={imageAlt} />
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{
        __html: `{ "@context": "http://schema.org", "@type": "LocalBusiness", "name": "${title}", "description": "${description}" }`,
      }}
    />
  </>
)

export default Metahead
