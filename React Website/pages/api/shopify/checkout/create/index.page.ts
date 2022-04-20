import type { NextApiHandler } from 'next'
import shopifyClient from 'Config/shopify-configuration'

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'POST') {
    res.status(404).json(null)
    return
  }

  const cart = await shopifyClient.checkout.create()
  res.status(200).json(cart)
}

export default handler
