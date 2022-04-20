import React from 'react'
import Prismic from 'prismic-javascript'
import { Client } from 'Config/prismic-configuration'
import { PageComponent } from 'Helpers/pageData'
import Metahead from 'Components/Metahead'
import Head from 'next/head'
import { PageStyle } from './styles'

const Transformations = (props: any) => (
  <>
    <Head>
      <title>Team RH | {props.meta?.page_title}</title>
      <Metahead
        description={props.meta?.description}
        keywords={props.meta?.keywords}
        sitename={'Team RH Fitness'}
        title={`Team RH | ${props.meta?.page_title}`}
        url={`https://www.teamrhfitness.com/${props.id}`}
        imageUrl={props.meta?.page_image}
        imageAlt={props.meta?.image_alt}
      />
    </Head>
    <PageStyle>
      <PageComponent
        page_data={props.page_data}
        components_data={props.components_data}
      />
    </PageStyle>
  </>
)

export async function getStaticProps ({ params, preview = null, previewData = {} }) {
  try {
    const pageId = process.env.NEXT_PUBLIC_ENV === 'production' ? 'YEgbSRAAACUAJF7e' : 'YhOKyBEAACgArq_r'

    const page = await Client().query(
      Prismic.Predicates.at('document.id', pageId)
    )

    const data = page.results[0].data

    const pageComponents = data.body.filter((item) =>
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

    return {
      props: {
        id: 'transformations',
        meta: {
          page_title: data.page_title?.[0]?.text || null,
          description: data.description?.[0]?.text || null,
          keywords: data.keywords?.[0]?.text || null,
          page_image: data.page_image?.url || null,
          image_alt: data.page_image?.alt || null,
        },
        page_data: data.body,
        componentsData,
      },
    }
  } catch (e) {
    console.log(e)
  }
}

export default Transformations
