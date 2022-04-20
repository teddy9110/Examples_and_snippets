import React, { Fragment } from 'react'
import Prismic from 'prismic-javascript'
import { Client } from 'Config/prismic-configuration'
import Head from 'next/head'
import Metahead from 'Components/Metahead'
import { PageComponent } from 'Helpers/pageData'
import { BlogHeader, Body } from 'StyleGuide/Blogs/styleguide'
import { RichText } from 'prismic-reactjs'
import { PageStyle, TransformationEmbed } from './styles'
import PrismicImage from 'Components/image/PrismicImage'
import createHtmlSerializer from 'Helpers/htmlSerializer'

const htmlSerializer = createHtmlSerializer({
  createImageStyle: () => ({
    image: {
      width: '100%',
    },
  }),
})

const Index = (props: any) => {
  const {
    meta,
    transformationTitle,
    transformationImage,
    transformationBody,
    transformation = {},
    components,
  } = props

  return (
    <>
      { transformation && (
        <>
          <Head>
            <title> Team RH | { meta.title }</title>
            <Metahead
              description={ meta.description }
              keywords={ meta.keywords }
              sitename={'Team RH Fitness'}
              title={`Team RH |  ${meta.title} ` }
              url={`https://www.teamrhfitness.com/transformations/${meta.slug}`}
              imageUrl={ meta.image }
              imageAlt={ meta.alt }
            />
          </Head>
          <PageStyle>
            <>
              <BlogHeader>
                <section className="title">
                  <h1>{transformationTitle[0].text}</h1>
                </section>
                <section className="image">
                  <PrismicImage
                    loading="lazy"
                    url={transformationImage?.url}
                    alt={transformationImage?.alt ?? transformationTitle[0].text ?? ''}
                    width={transformationImage.dimensions.width}
                    height={transformationImage.dimensions.height}
                  />
                </section>
              </BlogHeader>

              <Body>
                {transformationBody?.map((post, index) => (
                  <Fragment key={index}>
                    {post.type === 'embed' && (
                      <TransformationEmbed
                        dangerouslySetInnerHTML={{
                          __html: post.oembed.html.replace(200, 'auto').replace(113, '400'),
                        }}
                      />
                    )}
                    {post.type !== 'embed' && (
                      <RichText
                        render={[post]}
                        htmlSerializer={htmlSerializer}
                      />
                    )}
                  </Fragment>
                ))}
              </Body>
            </>
          </PageStyle>
          <PageComponent
            page_data={transformation}
            components_data={components}
          />
        </>
      )}
    </>
  )
}

export async function getServerSideProps ({ query, preview = null, previewData = {} }) {
  const id = query.id

  const transformation = await Client().query(
    Prismic.Predicates.at('my.blogad.uid', id)
  )

  const data = transformation.results[0].data

  const pageComponents = data.components.filter((item) =>
    item.primary.component ? item.primary.component : false
  )

  const componentsData = await Promise.all(
    pageComponents.map(async (item) => {
      const data = await Client().query(
        Prismic.Predicates.at('document.type', item.primary.component)
      )

      return { name: item.primary.component, ...data }
    })
  )

  if (!transformation) {
    return {
      notFound: true,
    }
  }

  return {
    props: {
      preview,
      meta: {
        title: data?.page_title?.map((post) => { return post.text }) || null,
        description: data?.description?.map((post) => { return post.text }) || null,
        keywords: data?.keywords?.map((post) => { return post.text }) || null,
        image: data.page_image?.url || null,
        alt: data.page_image?.alt || null,
        slug: id,
      },
      transformation: data.components,
      transformationImage: data.featured_image,
      transformationTitle: data.title,
      transformationBody: data.body,
      components: componentsData,
    },
  }
}

export default Index
