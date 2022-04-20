import React, { useMemo, useState } from 'react'
import Button from 'Components/primitives/form/Button'
import Head from 'next/head'
import Metahead from 'Components/Metahead'
import Input from 'Components/primitives/form/inputs/Input'
import Select from 'Components/primitives/form/inputs/Select'
import AddToCartForm from 'Components/AddToCartForm'
import PriceComparison from 'Components/PriceComparison'
import CustomError from 'Components/CustomError'
import PromotedDialog from 'Components/PromotedDialog'
import SwiperCore, { Pagination, A11y, Autoplay, Navigation } from 'swiper'
import { Swiper, SwiperSlide } from 'swiper/react'
import { DateTime } from 'luxon'
import { AccordianBase, PageStyle, ProductControls, ProductGrid } from './styles'
import { fetchByHandle } from 'Pages/api/shopify/product/fetch-by-handle/[id]/index.page'
import shopifyClient from 'Config/shopify-configuration'

const Accordian = (props: any) => {
  const [active, setActive] = useState(true)
  const toggle = () => setActive(!active)
  return (
    <AccordianBase className={active ? 'active' : ''}>
      <header onClick={toggle}>
        {props.title}
        <i className="fa fa-chevron-down" aria-hidden="true"></i>
        <i className="fa fa-chevron-up" aria-hidden="true"></i>
      </header>
      <div>{props.children}</div>
    </AccordianBase>
  )
}

interface Props {
  productInfo: any
  isPreorder?: boolean
  availableOn?: string
}

const Index = ({ productInfo, isPreorder, availableOn }: Props) => {
  const [loading, setLoading] = useState(false)
  const [sku, setSku] = useState(productInfo?.variants?.edges[0]?.node.sku)

  const hasStock = useMemo(() => {
    return (productInfo?.variants?.edges ?? [])
      .filter(({ node: { availableForSale } }) => availableForSale)
      .length > 0
  }, [])

  const [promotionDialog, setPromotionDialog] = useState({
    title: null,
    description: '',
    open: false,
  })

  SwiperCore.use([Autoplay, Pagination, A11y, Navigation])

  if (!productInfo) {
    return (
      <PageStyle>
        <CustomError />
      </PageStyle>
    )
  }

  return (
    <PageStyle>
      {productInfo && (
        <>
          <Head>
            <title>Team RH | {productInfo.title}</title>
            <Metahead
              description={productInfo.description.substring(0, 200)}
              keywords={''}
              sitename={'Team RH Fitness'}
              title={`Team RH | ${productInfo.title}`}
              url={`https://www.teamrhfitness.com/store/${productInfo.handle}`}
              imageUrl={
                productInfo?.images?.edges?.[productInfo.images.edges.length - 1].node.src
              }
              imageAlt={
                productInfo?.images?.edges?.[productInfo.images.edges.length - 1].node.altText
              }
            />
          </Head>
          <ProductGrid>
            <section className="slider-container">
              <Swiper
                navigation
                slidesPerView={1}
                pagination={{ clickable: true }}
              >
                {productInfo.images?.edges?.map((item, index) => (
                  <SwiperSlide key={item.node.src}>
                    <img
                      loading="lazy"
                      id={`product_image_${index}`}
                      src={item.node.src}
                      alt={item.node.altText}
                      width="600px"
                      height="600px"
                    />
                  </SwiperSlide>
                ))}
              </Swiper>
            </section>
            <section className="details">
              <h1>{productInfo.title}</h1>
              <PriceComparison
                variant={productInfo.variants?.edges[0].node}
              />
              <div className="available-on">
                {isPreorder && `Available on ${DateTime.fromISO(availableOn).toLocaleString(DateTime.DATE_MED)}`}
                {!isPreorder && (productInfo.availableForSale ? 'In Stock' : 'Sold Out')}
              </div>
              {hasStock && (
                <div className="add-form">
                  <AddToCartForm
                    setLoading={setLoading}
                    setPromotionDialog={setPromotionDialog}
                  >
                    <ProductControls>
                      {(productInfo.variants?.edges || []).length === 1
                        ? (
                          <>
                            <input
                              type="hidden"
                              name="variantId"
                              value={productInfo.variants.edges[0].node.id}
                            />
                            <input
                              type="hidden"
                              name="sku"
                              value={productInfo.variants.edges[0].node.sku}
                            />
                          </>
                        )
                        : (
                          <>
                            <Select
                              name="variantId"
                              label={productInfo.options[0].name}
                              onChange={(e) => {
                                setSku(e.target.options[e.target.selectedIndex].id)
                              }}
                            >
                              {productInfo.variants.edges.map((item) => (
                                <option
                                  key={item.node.sku}
                                  id={item.node.sku}
                                  value={item.node.id}
                                  disabled={!item.node.availableForSale}
                                >
                                  {item.node.title}
                                  {item.node.availableForSale ? '' : ' out of stock'}
                                </option>
                              ))}
                            </Select>
                            <input type="hidden" name="sku" value={sku} />
                          </>
                        )}

                      <Input
                        label="Quantity"
                        min="1"
                        type="number"
                        name="quantity"
                        defaultValue={1}
                      />
                    </ProductControls>

                    {hasStock
                      ? (
                        <>
                          <Button
                            style={{ width: '100%', maxWidth: '100%' }}
                            loading={loading}
                            type="submit"
                          >
                            <i className="fa fa-shopping-cart" aria-hidden="true"></i>{' '}
                            {isPreorder ? 'Pre-order' : 'Add'}
                          </Button>
                        </>
                      )
                      : 'Sold Out'}

                  </AddToCartForm>
                </div>
              )}
              <Accordian title={<h3>Product Description</h3>}>
                <div
                  dangerouslySetInnerHTML={{
                    __html: productInfo.descriptionHtml,
                  }}
                ></div>
              </Accordian>
            </section>
          </ProductGrid>
        </>
      )}
      <PromotedDialog
        open={promotionDialog.open}
        title={promotionDialog.title}
        data={promotionDialog}
        control={() => {
          setPromotionDialog({ ...promotionDialog, open: false })
          location.replace('/store/cart')
        }}
      />
    </PageStyle>
  )
}

export async function getStaticProps ({ params }) {
  try {
    const productInfo = await fetchByHandle(params.id) as any

    const preorderTag = (productInfo.tags || [] as string[])
      .find((item) => item.startsWith('preorder'))

    const availableOn = !preorderTag
      ? null
      : preorderTag.split('_')[1]

    return {
      props: {
        productInfo,
        isPreorder: !!preorderTag,
        availableOn,
      },
      revalidate: 60,
    }
  } catch (e) {
    console.log(e)
    return { props: { product_info: null }, revalidate: 60 }
  }
}

export async function getStaticPaths () {
  const products = await shopifyClient.product.fetchAll(200)
  const plainData = JSON.parse(JSON.stringify(products))

  return {
    paths: plainData.map((item) => ({
      params: {
        id: item.handle,
      },
    })),
    fallback: true,
  }
}

export default Index
