import React from 'react'
import Head from 'next/head'
import ResourceNotFoundError from 'errors/ResourceNotFoundError'
import BlogPostMeta from './meta'
import RichText from 'Components/RichText'
import { BlogHeader, Body } from 'StyleGuide/Blogs/styleguide'
import { PageStyle, Share, ShareTitle, BlogTitle, HeaderImage } from './styles'
import { BlogPost, findBlogPost } from 'Requests/blog-post'
import { transformRichText } from 'Components/RichText/transformRichText'

interface Props {
  blogPost: BlogPost
}

const BlogPostPage = ({ blogPost }: Props) => {
  const featuredImage = blogPost.attributes.featuredImage.data

  return (
    <>
      <Head>
        <title>{blogPost.attributes.title} | Team RH</title>
        <BlogPostMeta blogPost={blogPost} />
      </Head>
      <PageStyle>
        <>
          <BlogHeader>
            <section className="title">
              <span className="category"></span>
              <BlogTitle>{blogPost.attributes.title}</BlogTitle>
            </section>
            <section className="image" style={{ marginTop: '18px' }}>
              <HeaderImage
                loading="lazy"
                imageData={featuredImage}
              />
            </section>
          </BlogHeader>

          <Body>
            <RichText>{blogPost.attributes.contentHtml}</RichText>

            {typeof window !== 'undefined' && (
              <Share>
                <ShareTitle>Share On</ShareTitle>
                <a
                  href={`https://www.facebook.com/sharer/sharer.php?u=${window.location.href}`}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <i className="fa fa-facebook" aria-hidden="true"></i>
                </a>
                <a
                  href={`https://twitter.com/share?text=${blogPost.attributes.title}&url=${window.location.href}`}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <i className="fa fa-twitter" aria-hidden="true"></i>
                </a>
              </Share>
            )}
          </Body>
        </>
      </PageStyle>
    </>
  )
}

export async function getServerSideProps ({ query }: { query: { id: string } }) {
  try {
    const blogPost = await findBlogPost(query.id)
    blogPost.attributes.contentHtml = transformRichText(blogPost.attributes.contentHtml)

    return {
      props: {
        blogPost,
      },
    }
  } catch (e) {
    if (e instanceof ResourceNotFoundError) {
      return {
        notFound: true,
      }
    }

    throw e
  }
}

export default BlogPostPage
