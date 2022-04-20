import type { NextApiHandler } from 'next'
import { shopifyAxios } from 'Config/shopify-configuration'
import { query } from 'GraphQL/products'
import transfromShopifyProducts from 'Transformers/shopify-product-transformer'

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const { query: searchQuery } = req.query
  const { data: { data } } = await shopifyAxios.post(
    '2022-01/graphql.json',
    query(searchQuery as string)
  )

  const result = transfromShopifyProducts(data.products.edges)
  res.status(200).json(result)
}

export default handler
