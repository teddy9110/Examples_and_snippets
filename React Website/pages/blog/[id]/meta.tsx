import React from 'react'
import MetaData from 'Components/MetaData'
import cheerio from 'cheerio'
import { BlogPost } from 'Requests/blog-post'
import { truncate } from 'Helpers/string'
import { getLargestImageUrl } from 'Helpers/strapi/image'

interface Props {
  blogPost: BlogPost
}

const BlogPostMeta = ({ blogPost }: Props) => {
  const featuredImage = blogPost.attributes.featuredImage.data?.attributes
  const metadata = blogPost.attributes?.metadata
  const metadataImage = metadata?.image?.data?.attributes

  const thumbnailPath = getLargestImageUrl(metadata?.image?.data ?? blogPost.attributes.featuredImage.data)
  const thumbnailAlt = (metadataImage ?? featuredImage)?.alternativeText

  return (
    <MetaData
      title={
        metadata.title ??
        blogPost.attributes.title
      }
      description={
        metadata.description ??
        truncate(cheerio.load(blogPost.attributes.contentHtml)('p').first().text(), 200) ??
        metadata.title ??
        blogPost.attributes.title
      }
      keywords={metadata.keywords}
      urlPath={`blog/${blogPost.attributes.slug}`}
      thumbnailUrl={thumbnailPath}
      thumbnailAlt={thumbnailAlt ?? 'Missing Image'}
    />
  )
}

export default BlogPostMeta
