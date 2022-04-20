import React, { useState } from 'react'
import Button from 'Components/primitives/form/Button'
import { Client } from 'Config/prismic-configuration'
import { RichText } from 'prismic-reactjs'
import Head from 'next/head'
import Dialog from 'Components/Dialog'
import Prismic from 'prismic-javascript'
import { PageComponent } from 'Helpers/pageData'
import Metahead from 'Components/Metahead'
import CustomError from 'Components/CustomError'
import CustomPageBody from 'Components/CustomPageBody'
import { PageStyle, PageHeader, PageContent } from './styles'

interface Props {
  postData: any
  components: any
  componentsData: any
}

const Index = ({ postData = {}, components = {}, componentsData = {} }: Props) => {
  const [videoModal, showVideoModal] = useState(false)
  if (!postData.data) {
    return (
      <PageStyle>
        <CustomError />
      </PageStyle>
    )
  }

  return (
    <>
      {postData && (
        <>
          <Head>
            <title>{postData.data?.meta_title.map((post) => { return post.text })} | Team RH</title>
            <Metahead
              sitename={'Team RH Fitness'}
              title={`Team RH | ${postData.data?.meta_title.map((post) => { return post.text })}`}
              url={`https://www.teamrhfitness.com/blog/${postData.uid}`}
              imageUrl={postData?.data?.link_image?.url}
              imageAlt={postData?.data?.link_image?.alt}
            />
          </Head>
          <PageStyle>
            <>
              <PageHeader>
                <section className="spacing-mobile">
                  <h3> Hi <span className="header-coloured">{postData?.data?.customers_name.map((post) => { return post.text })}</span> <img style={{ maxWidth: '30px' }} src='/images/wave.png' /></h3>
                  <span className="introduction"> {RichText.render(postData?.data?.introduction)} </span>
                </section>
              </PageHeader>
              <PageContent>
                <div className="centered-div">
                  <h3> Your Details and our plan:</h3>
                </div>
                {postData.data?.body.map(object => {
                  return <CustomPageBody data={object} key={object.slice_type} />
                })}
                <div className="centered-div button-group">
                  <Button href="/pricing">
                    Start your journey
                  </Button>
                  <a
                    className="tour-link"
                    href=""
                    onClick={(e) => {
                      e.preventDefault()
                      showVideoModal(true)
                    }}>
                    Take a Tour of the app
                  </a>

                </div>

                <PageComponent
                  page_data={postData.data.body}
                  components_data={componentsData}
                />
              </PageContent>

            </>
          </PageStyle>
          <Dialog
            control={() => showVideoModal(false)}
            open={videoModal}
            title={'Tour Of The App'}
          >
            <p>
              <iframe
                width="100%"
                height="315"
                src="https://www.youtube.com/embed/yLDlGCxJaxw"
                title="YouTube video player"
                frameBorder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowFullScreen
              ></iframe>
            </p>
          </Dialog>
        </>
      )}
    </>
  )
}

export async function getServerSideProps ({ query, preview = null, previewData = {} }: any) {
  const id = query.id
  const { ref } = previewData

  const post = await Client().getByUID('personalised_page', id, ref ? { ref } : null ?? {})

  const pageComponents = post.data.body.filter((item) =>
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
  if (!post) {
    return {
      notFound: true,
    }
  }
  return {
    props: {
      componentsData,
      preview,
      postData: post,
    },
  }
}

export default Index
