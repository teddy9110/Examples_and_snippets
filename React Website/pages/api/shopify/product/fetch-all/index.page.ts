import type { NextApiHandler } from 'next'
import shopifyClient from 'Config/shopify-configuration'

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const { page_size: pageSizeString = '200' } = req.query
  const pageSize = parseInt(pageSizeString as string)
  const products = await shopifyClient.product.fetchAll(pageSize)

  res.status(200).json(products)
}

export default handler
