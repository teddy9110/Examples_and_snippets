import type { NextApiHandler } from 'next'
import shopifyClient from 'Config/shopify-configuration'

const handler: NextApiHandler = async (req, res) => {
  if (req.method !== 'GET') {
    res.status(404).json(null)
    return
  }

  const { cart_id: cartId } = req.query

  const cart = await shopifyClient.checkout.fetch(cartId as string)
  res.status(200).json(cart)
}

export default handler
