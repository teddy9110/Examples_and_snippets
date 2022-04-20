import React from 'react'
import BlogCard from 'Components/BlogCard'
import Head from 'next/head'
import MetaData from 'Components/MetaData'
import Select from 'Components/primitives/form/inputs/Select'
import PageSelector from 'Components/PageSelector'
import cheerio from 'cheerio'
import { BlogPost, getBlogPosts } from 'Requests/blog-post'
import { BlogCategory, getBlogCategories } from 'Requests/blog-category'
import { StrapiCollectionPagination } from 'Helpers/strapi/collections'
import { getLargestImageUrl } from 'Helpers/strapi/image'
import { truncate } from 'Helpers/string'

import {
  BlogList, FeaturedBlogPost, FeaturedHeader, Filters,
  PageStyle, NewsLetter, PageHeader,
} from './styles'

interface Props {
  blogPosts: BlogPost[]
  blogCategories: BlogCategory[]
  pagination: StrapiCollectionPagination
}

const Index = ({ blogPosts: allBlogPosts, blogCategories, pagination }: Props) => {
  const blogPosts = allBlogPosts.slice(1)
  const featuredBlogPost = allBlogPosts[0]
  const featuredBlogPostImage = featuredBlogPost.attributes.featuredImage.data

  return (
    <>
      <Head>
        <title>Team RH | Blog</title>
        <MetaData
          title="Blog"
          description="We don't do boring, find all the latest fat loss tips, tricks and news from the Team RH community right here."
          keywords="team rh, blog, news, community, no boring, weight loss, fat loss, recipes, advice"
          urlPath="blog"
          thumbnailUrl={getLargestImageUrl(featuredBlogPostImage, 'large')}
          thumbnailAlt={featuredBlogPostImage?.attributes?.alternativeText ?? 'Image Missing'}
        />
      </Head>
      <PageHeader>
        <h1>
          Team RH <strong>Blog</strong>
        </h1>
        <p>
          {`
            We don't do boring, find all the latest fat loss tips, tricks and news
            from the Team RH community right here.
          `}
        </p>
      </PageHeader>
      <Filters>
        <Select
          className="collection-select"
          onChange={(e) => {
            location.replace(e.target.value)
          }}
        >
          <option selected disabled>
            Categories
          </option>
          <option value="/blog">All</option>
          {blogCategories.map(({ attributes: { slug, name } }) => (
            <option key={`category:${slug}`} value={`/blog/category/${slug}`}>
              {name}
            </option>
          ))}
        </Select>
      </Filters>

      <PageStyle>
        {(() => {
          const {
            slug,
            title,
            contentHtml: content,
            featuredImage,
            category,
          } = featuredBlogPost.attributes

          const {
            alternativeText,
            width,
            height,
          } = featuredImage?.data?.attributes ?? {}

          return (
            <FeaturedHeader>
              <FeaturedBlogPost>
                <a href={`/blog/${slug}`}>
                  <img
                    loading="lazy"
                    src={getLargestImageUrl(featuredImage.data, 'large')}
                    alt={alternativeText ?? 'Missing Image'}
                    width={width}
                    height={height}
                  />
                  <section>
                    <span className="category">
                      {category?.slug}
                    </span>
                    <h3>{title}</h3>
                    <p>{truncate(cheerio.load(content)('p').first().text(), 200, 50)}</p>
                  </section>
                </a>
              </FeaturedBlogPost>
              <section className="newsletter">
                <NewsLetter>
                  <section>
                    <i className="fa fa-envelope" aria-hidden="true"></i>
                    <h3>Get the latest news</h3>
                    <iframe
                      width="540"
                      height="300"
                      src="https://f63d4ddb.sibforms.com/serve/MUIEABBKIBS4HAV4gDEMUMQiCOotMyyMLXbHcDqgEEnjeX7Vj0onfYMbRFrIgPL_C42672ysrw9f_xv6UmK6lbfsrBNdQ7VWIeDa5swIcpLONhDHboeklME8EoA2CnTFa0M5QPcLd6R4dg-lKnaOzQZgsbvIlBIATCoseIf1m-HpiUCUPqct8RO8lKanL4eU9QeNyjdJ4c34U5Dr"
                      frameBorder="0"
                      scrolling="no"
                      allow="fullscreen"
                      style={{ width: '100%' }}
                    ></iframe>
                    <p className="small">
                      For more about how we use your information, see our{' '}
                      <a href="/privacy-policy">Privacy Notice.</a>
                    </p>
                  </section>
                </NewsLetter>
              </section>
            </FeaturedHeader>
          )
        })()}

        <BlogList style={{ padding: '0 10%' }}>
          {blogPosts.map(({
            attributes: {
              slug,
              title,
              contentHtml: content,
              category,
              featuredImage: { data: image },
            },
          }) => (
            <BlogCard key={`blog:${slug}`}>
              <a href={`/blog/${slug}`}>
                <img
                  loading="lazy"
                  src={getLargestImageUrl(image, 'medium')}
                  alt={image?.attributes?.alternativeText ?? 'Image Missing'}
                  width="377px"
                  height="223px"
                />
                <section>
                  <span className="category">{category?.slug}</span>

                  <h3>
                    {truncate(title, 46, 24)}
                  </h3>
                  <p>
                    {truncate(cheerio.load(content)('p').first().text(), 200, 40)}
                  </p>
                </section>
              </a>
            </BlogCard>
          ))}
        </BlogList>

        {pagination.pageCount > 1 && (
          <PageSelector
            page={pagination.page}
            pageCount={pagination.pageCount}
            createPageLink={(page) => `/blog?page=${page}`}
          />
        )}
      </PageStyle>
    </>
  )
}

export async function getServerSideProps ({ query, preview = null, previewData = {} }) {
  const page = query.page ? parseInt(query.page) : 1

  const blogPosts = await getBlogPosts({
    pagination: { page, limit: 22 },
  })

  const blogCategories = await getBlogCategories({
    pagination: { limit: 1000 },
  })

  if (blogPosts.length < 1) {
    return {
      notFound: true,
    }
  }

  return {
    props: {
      blogPosts,
      blogCategories,
      pagination: blogPosts.pagination,
    },
  }
}

export default Index
