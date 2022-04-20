import React from 'react'
import BlogCard from 'Components/BlogCard'
import Head from 'next/head'
import MetaData from 'Components/MetaData'
import PageSelector from 'Components/PageSelector'
import Select from 'Components/primitives/form/inputs/Select'
import cheerio from 'cheerio'
import { BlogList, PageStyle, PageHeader, Filters } from './styles'
import { BlogCategory, findBlogCategory, getBlogCategories } from 'Requests/blog-category'
import { BlogPost, getBlogPosts } from 'Requests/blog-post'
import { StrapiCollectionPagination } from 'Helpers/strapi/collections'
import { truncate } from 'Helpers/string'
import { getLargestImageUrl } from 'Helpers/strapi/image'

interface Props {
  blogCategory: BlogCategory
  blogCategories: BlogCategory[]
  blogPosts: BlogPost[]
  pagination: StrapiCollectionPagination
}

const Index = ({ blogCategory, blogCategories, blogPosts, pagination }: Props) => {
  const { attributes: { slug: categorySlug, name: categoryName } } = blogCategory
  const featuredBlogPost = blogPosts[0]
  const featuredBlogPostImage = featuredBlogPost.attributes.featuredImage.data

  return (
    <>
      <Head>
        <title>Team RH | Blog {categoryName}</title>
        <MetaData
          title="Blog Categories"
          description="We don't do boring, find all the latest fat loss tips, tricks and news from the Team RH community right here."
          keywords="team rh, blog, news, community, no boring, weight loss, fat loss, recipes, advice"
          urlPath="blog"
          thumbnailUrl={getLargestImageUrl(featuredBlogPostImage, 'large')}
          thumbnailAlt={featuredBlogPostImage?.attributes?.alternativeText ?? 'Image Missing'}
        />
      </Head>
      <PageHeader>
        <h1>{categoryName}</h1>
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
          <option value="/blog">all</option>
          {blogCategories.map(({ attributes: { slug, name } }) => (
            <option
              key={`blog_category:${slug}`}
              value={`/blog/category/${slug}`}
            >
              {name}
            </option>
          ))}
        </Select>
      </Filters>

      <PageStyle>
        <BlogList style={{ padding: '0 10%' }}>
          {blogPosts.map(({
            attributes: {
              slug,
              title,
              contentHtml: content,
              category,
              featuredImage: {
                data: featuredImage,
              },
            },
          }) => (
            <BlogCard key={`blog:${slug}`}>
              <a href={`/blog/${slug}`}>
                <img
                  loading="lazy"
                  src={getLargestImageUrl(featuredImage, 'medium')}
                  alt={featuredImage?.attributes?.alternativeText ?? 'Image Missing'}
                />
                <section>
                  <span className="category">
                    {category?.slug}
                  </span>
                  <h3>{title}</h3>
                  <p>{truncate(cheerio.load(content)('p').first().text(), 200)}</p>
                </section>
              </a>
            </BlogCard>
          ))}
        </BlogList>

        {pagination.pageCount > 1 && (
          <PageSelector
            page={pagination.page}
            pageCount={pagination.pageCount}
            createPageLink={(page) => `/blog/category/${categorySlug}?page=${page}`}
          />
        )}
      </PageStyle>
    </>
  )
}

export async function getServerSideProps ({ query, preview = null, previewData = {} }: any) {
  const categoryName = query.id
  const page = query.page ? parseInt(query.page) : 1

  const blogCategory = await findBlogCategory(categoryName)
  const blogCategories = await getBlogCategories({
    pagination: { limit: 1000 },
  })

  const blogPosts = await getBlogPosts({
    filter: [
      {
        field: '[category][slug]',
        operation: '$eq',
        value: categoryName,
      },
    ],
    pagination: {
      page,
      limit: 15,
    },
  })

  if (blogPosts.length < 1) {
    return {
      notFound: true,
    }
  }

  return {
    props: {
      blogCategory,
      blogCategories,
      blogPosts,
      pagination: blogPosts.pagination,
    },
  }
}

export default Index
