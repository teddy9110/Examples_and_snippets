import React from 'react'
import Prismic from 'prismic-javascript'
import { Client as PClient } from 'Config/prismic-configuration'
import SwiperCore, { Pagination, A11y, Autoplay } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import ShopSearch from 'Components/ShopSearch'
import Metahead from 'Components/Metahead'
import Head from 'next/head'
import { BannerLink, PageStyle, CollectionList } from './styles'
import shopifyClient from 'Config/shopify-configuration'

const Index = ({ landingData }: any) => {
  SwiperCore.use([Autoplay, Pagination, A11y])

  return (
    <>
      <Head>
        <title>
          Team RH |{' '}
          {landingData[0]?.data?.title?.[0].text ||
            'Browse the Team RH products'}
        </title>
        <Metahead
          description={landingData[0].data?.description?.[0].text.substring(
            0,
            200
          )}
          keywords={landingData[0].data?.keywords || ''}
          sitename={'Team RH Fitness'}
          title={`Team RH | ${landingData[0]?.data?.title?.[0].text}`}
          url={'https://www.teamrhfitness.com/store'}
          imageUrl={landingData[0]?.data?.page_image?.url}
          imageAlt={landingData[0]?.data?.page_image?.alt}
        />
      </Head>

      <PageStyle>
        <ShopSearch />

        <section style={{ margin: 0 }}>
          <Swiper
            slidesPerView={1}
            pagination={{ clickable: true }}
            style={{ marginBottom: '4rem' }}
          >
            {landingData[0].data.banner.map((item) => (
              <SwiperSlide key={item.banner_image?.mobile?.url}>
                <BannerLink href={item.banner_link[0].text}>
                  <picture>
                    <source
                      srcSet={item.banner_image?.mobile?.url}
                      media="(max-width: 901px)"
                    />
                    <img
                      loading="lazy"
                      src={item.banner_image?.url}
                      alt={item.banner_image?.alt || ' '}
                    />
                  </picture>
                </BannerLink>
              </SwiperSlide>
            ))}
          </Swiper>
        </section>
        <div>
          <h1>Browse Products</h1>
          <CollectionList>
            {landingData[0].data.shop_collections.map((item) => (
              <>
                <a href={item.url[0].text} key={item.image.url}>
                  <h3>{item.name[0].text}</h3>
                  <section className="img">
                    <img
                      src={item.image.url}
                      alt={item.image.alt}
                      loading="lazy"
                    />
                  </section>
                </a>
              </>
            ))}
          </CollectionList>
        </div>
      </PageStyle>
    </>
  )
}

export async function getStaticProps (context) {
  try {
    const products = await shopifyClient.product.fetchAll(200)
    const collections = await shopifyClient.collection.fetchAllWithProducts()

    const landingData = await PClient().query(
      Prismic.Predicates.at('document.type', 'shop_landing')
    )

    const plainProductsData = JSON.parse(JSON.stringify(products))
    const plainCollectionData = JSON.parse(JSON.stringify(collections))

    return {
      props: {
        products: plainProductsData,
        collections: plainCollectionData,
        landingData: landingData.results,
      },
      revalidate: 1,
    }
  } catch (e) {
    console.log(e)
    return { props: { products: {} }, revalidate: 60 }
  }
}

export default Index
