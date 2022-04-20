import React from 'react'
import Prismic from 'prismic-javascript'
import { Client } from 'Config/prismic-configuration'
import pageComponents from 'Components/pageComponents'
import CustomError from 'Components/CustomError'

export const getPageData = async (docType, id, previewData: { ref?: any } = {}) => {
  const { ref } = previewData
  const page = await Client().getByUID('page', id, ref ? { ref } : null ?? {})

  const pageData = page.data

  const components = pageData.body.filter((item) =>
    item.primary.component ? item.primary.component : false
  )

  const componentsData = await Promise.all(
    components.map(async (item) => {
      const data = await Client().query(
        Prismic.Predicates.at('document.type', item.primary.component)
      )

      return { name: item.primary.component, ...data }
    })
  )

  return {
    meta: {
      page_title: pageData.page_title?.[0]?.text || null,
      description: pageData.description?.[0]?.text || null,
      keywords: pageData.keywords?.[0]?.text || null,
      page_image: pageData.page_image?.url || null,
      image_alt: pageData.page_image?.alt || null,
    },
    id: id,
    page_data: pageData.body,
    components_data: componentsData,
  }
}

export const PageComponent = (props: any) => {
  const { page_data: pageData = [], components_data: componentsData = [] } = props

  if (pageData.length === 0) {
    return <CustomError statusCode={404} />
  }

  return (
    <>
      {pageData.map((item, index) => {
        if (item.slice_type === 'component') {
          if (pageComponents[item.primary.component]) {
            const Component = pageComponents[item.primary.component]

            return (
              <Component
                key={`component_${index}`}
                block_data={item}
                data={componentsData.find(
                  (cData) => cData.name === item.primary.component
                )}
              />
            )
          }
        }

        if (pageComponents[item.slice_type]) {
          const Component = pageComponents[item.slice_type]
          return <Component key={`component_${index}`} data={item} />
        }
        return null
      })}
    </>
  )
}
