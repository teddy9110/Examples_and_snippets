import type { NextApiHandler } from 'next'
import shopifyClient from 'Config/shopify-configuration'

const handler: NextApiHandler = async (req, res) => {
  const {
    first: firstString,
    products_first: productsFirstString,
  } = req.query

  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const productsFirst = productsFirstString ? parseInt(productsFirstString as string) : undefined
  const first = firstString ? parseInt(firstString as string) : undefined

  const collections = await shopifyClient.collection.fetchAllWithProducts({ productsFirst, first })
  res.status(200).json(collections)
}

export default handler
