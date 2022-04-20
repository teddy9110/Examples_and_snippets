import type { NextApiHandler } from 'next'
import type { Product } from 'shopify-buy'
import { shopifyAxios } from 'Config/shopify-configuration'
import { productByHandle } from 'GraphQL/products'

export const fetchByHandle = async (id: string): Promise<Product> => {
  const {
    data: {
      data: { productByHandle: product },
    },
  } = await shopifyAxios.post(
    '2022-01/graphql.json',
    productByHandle(id)
  )

  return product
}

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const { id } = req.query
  const product = await fetchByHandle(id as string)

  if (product === null) {
    res.status(404).json(null)
    return
  }

  res.status(200).json(product)
}

export default handler
