import React from 'react'
import { PageComponent, getPageData } from 'Helpers/pageData'
import Head from 'next/head'
import Metahead from 'Components/Metahead'

const Index = (props: any) => {
  return (
    <>
      <Head>
        <title>Team RH | {props?.meta?.page_title}</title>
        <Metahead
          description={props?.meta?.description}
          keywords={props?.meta?.keywords}
          sitename={'Team RH Fitness'}
          title={`Team RH | ${props?.meta?.page_title}`}
          url={'https://www.teamrhfitness.com/'}
          imageUrl={props?.meta?.page_image}
          imageAlt={props?.meta?.image_alt}
        />
      </Head>
      <PageComponent
        page_data={props.page_data}
        components_data={props.components_data}
      />
    </>
  )
}

export async function getStaticProps (context, req) {
  const props = await getPageData('page', 'homepage')

  return {
    props: {
      ...props,
    },
    revalidate: 60,
  }
}

Index.mastStyle = 'transparent'

export default Index
