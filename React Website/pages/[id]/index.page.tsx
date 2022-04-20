import React from 'react'
import { PageComponent, getPageData } from 'Helpers/pageData'
import Prismic from 'prismic-javascript'
import { Client } from 'Config/prismic-configuration'
import { PageStyle } from './styles'
import Head from 'next/head'
import Metahead from 'Components/Metahead'

const Index = (props: any) => (
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

export async function getStaticPaths () {
  const pagesList = await Client().query(
    Prismic.Predicates.at('document.type', 'page'),
    { pageSize: 200 }
  )

  const pages = pagesList.results
    .filter((item) => item.uid !== 'homepage')
    .filter((item) => item.uid !== 'transformations')
    .filter((item) => item.uid !== 'personalised_page')
    .filter((item) => item.uid !== 'contact-us')
    .filter((item) => item.uid !== 'terms')
    .map((item) => ({ params: { id: item.uid } }))

  return {
    paths: pages,
    fallback: true,
  }
}

export async function getStaticProps ({ params, preview = null, previewData = {} }) {
  try {
    const props = await getPageData('page', params.id, previewData)

    return {
      props: {
        preview,
        ...props,
      },
      revalidate: 60,
    }
  } catch (e) {
    console.log(e)
  }
}

export default Index
