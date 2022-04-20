import React, { Fragment, useState } from 'react'
import Select from 'Components/primitives/form/inputs/Select'
import ProductStore from 'Store/products'
import ShopSearch from 'Components/ShopSearch'
import ProductCard from 'Components/ProductCard'
import ProductGrid from 'Components/ProductGrid'
import PromotedDialog from 'Components/PromotedDialog'
import Head from 'next/head'
import Metahead from 'Components/Metahead'
import { PageHeader, PageStyle } from './styles'
import shopifyClient from 'Config/shopify-configuration'
import { fetchSorted } from 'Pages/api/shopify/product/fetch-sorted/index.page'

const filters = [
  { label: 'Price high to low', value: 'PRICE-true' },
  { label: 'Price low to high', value: 'PRICE-false' },
  { label: 'New to old', value: 'CREATED-true' },
  { label: 'Old to new', value: 'CREATED-false' },
]

interface Props {
  id: string
  products: any[]
  collections: any[]
  info: {
    image: any
  }
  title: string
  product_data: any[]
}

const Index = ({ id, products = [], collections = [], info, title = '' }: Props) => {
  const [promotionDialog, setPromotionDialog] = useState({
    title: null,
    description: '',
    open: false,
  })

  const { filterProducts, filteredProducts } = ProductStore((state) => ({
    filterProducts: state.filterProducts,
    filteredProducts: state.filteredProducts,
  }))

  return (
    <PageStyle>
      {collections && (
        <>
          <Head>
            <title>Team RH | {title}</title>
            <Metahead
              description={`${title.replace('-', ' ')} products`}
              keywords={''}
              sitename={'Team RH Fitness'}
              title={`Team RH | Shop ${title.replace('-', ' ')}`}
              url={`https://www.teamrhfitness.com/store/collections/${title}`}
              imageUrl={info?.image?.src}
              imageAlt={info?.image?.altText}
            />
          </Head>
          <ShopSearch />
          <PageHeader>
            <h1>{title}</h1>
            <div className="filters">
              <Select
                className="collection-select"
                onChange={(e) => {
                  location.replace(`/store/collections/${e.target.value}`)
                }}
              >
                <option selected disabled>
                  Collections
                </option>
                {collections.map((item) => (
                  <option
                    key={item.handle.toLowerCase()}
                    value={item.handle.toLowerCase()}
                  >
                    {item.handle.replace('-', ' ')}
                  </option>
                ))}
              </Select>
              <Select
                onChange={(e) => {
                  filterProducts(id, (e.target.value))
                    .catch((e) => console.log(e))
                }}
              >
                <option selected disabled>
                  Sort by
                </option>
                {filters.map((item) => (
                  <option
                    key={item.value.toLowerCase()}
                    value={item.value.toLowerCase()}
                  >
                    {item.label}
                  </option>
                ))}
              </Select>
            </div>
          </PageHeader>
          <ProductGrid>
            {(filteredProducts.length !== 0 ? filteredProducts : products).map(
              (item) => (
                <Fragment key={item.id}>
                  <ProductCard
                    key={item.id}
                    handle={item.handle}
                    title={item.title}
                    images={item.images}
                    variants={item.variants}
                    availableForSale={item.availableForSale}
                    tags={item.tags}
                    isPreorder={item.isPreorder}
                    availableOn={item.availableOn}
                    setPromotionDialog={setPromotionDialog}
                  />
                </Fragment>
              )
            )}
          </ProductGrid>
          <PromotedDialog
            open={promotionDialog.open}
            title={promotionDialog.title}
            data={promotionDialog}
            control={() => {
              setPromotionDialog({ ...promotionDialog, open: false })
              location.replace('/store/cart')
            }}
          />
        </>
      )}
    </PageStyle>
  )
}

export async function getStaticPaths () {
  const collections = await shopifyClient.collection.fetchAllWithProducts()
  return {
    // Only `/posts/1` and `/posts/2` are generated at build time
    paths: collections.map((item) => ({
      params: {
        id: item.handle,
      },
    })),
    // Enable statically generating additional pages
    // For example: `/posts/3`
    fallback: true,
  }
}

export async function getStaticProps ({ params }) {
  try {
    const collections = await shopifyClient.collection.fetchAllWithProducts({
      productsFirst: 200,
    })

    const collection = await fetchSorted(params.id)
    const plainCollectionsData = JSON.parse(JSON.stringify(collections))
    const info = JSON.parse(JSON.stringify(collection))
    const plainProductsData = JSON.parse(JSON.stringify(collection))

    return {
      props: {
        id: params.id,
        title: params.id.replace('-', ' '),
        info: info,
        collections: plainCollectionsData,
        products: plainProductsData,
      },
      revalidate: 1,
    }
  } catch (e) {
    console.log(e)
    return { props: { products: [] }, revalidate: 60 }
  }
}

export default Index
