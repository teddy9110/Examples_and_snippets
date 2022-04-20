import React from 'react'
import Head from 'next/head'
import { Client } from 'Config/prismic-configuration'
import Metahead from 'Components/Metahead'
import ContactBanner from 'Components/contact-us/ContactUsBanner'
import ContactUsForm from 'Components/contact-us/ContactUsForm'
import ContactUsAddresses from 'Components/contact-us/ContactUsAddresses'
import { PageStyle } from './styles'
import CustomError from 'Components/CustomError'

interface Props {
  postData: any
}

const ContactUs = ({ postData = {} }: Props) => {
  if (!postData.data) {
    return (
      <PageStyle>
        <CustomError />
      </PageStyle>
    )
  }

  return (
    <>
      <Head>
        <Metahead
          description="Contact Us"
          keywords="Contact Us, help, contact, assistance"
          sitename={'Team RH Fitness'}
          title={'Team RH | Contact Us'}
          url={'https://www.teamrhfitness.com/contact-us'}
          imageUrl={postData.data.page_image.url}
          imageAlt={postData.data.page_image.alt}
        />
      </Head>

      <PageStyle>
        <ContactBanner />
        <ContactUsForm />
        <ContactUsAddresses />
      </PageStyle>
    </>
  )
}

export async function getServerSideProps ({ query, preview = null, previewData = {} }: any) {
  const { ref } = previewData
  const page = await Client().getByUID('page', 'contact-us', ref ? { ref } : null ?? {})

  if (!page) {
    return {
      notFound: true,
    }
  }

  return {
    props: {
      preview,
      postData: page,
    },
  }
}

export default ContactUs
