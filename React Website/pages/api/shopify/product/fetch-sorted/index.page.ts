import type { NextApiHandler } from 'next'
import { shopifyAxios } from 'Config/shopify-configuration'
import { collectionSort } from 'GraphQL/products'
import transfromShopifyProducts from 'Transformers/shopify-product-transformer'

export const fetchSorted = async (collectionName: string, sortBy?: string): Promise<any[]> => {
  const { data: { data } } = await shopifyAxios.post(
    '2022-01/graphql.json',
    collectionSort(collectionName, { sortBy, limit: 200 })
  )

  return transfromShopifyProducts(data.collectionByHandle.products.edges)
}

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const {
    collection_name: collectionName,
    sort_by: sortBy,
  } = req.query

  res
    .status(200)
    .json(await fetchSorted(collectionName as string, sortBy as string))
}

export default handler
