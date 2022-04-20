import type { NextApiHandler } from 'next'
import type { Cart } from 'shopify-buy'
import shopifyClient from 'Config/shopify-configuration'

export const VALID_METHODS = [
  'POST' as const,
  'PUT' as const,
  'DELETE' as const,
]

const handler: NextApiHandler = async (req, res) => {
  if (!VALID_METHODS.includes(req.method as any)) {
    res.status(404).json(null)
    return
  }

  const { cart_id: cartId } = req.query
  const items = req.body.items

  let cart: Cart
  switch (req.method) {
    case 'POST': {
      cart = await shopifyClient.checkout.addLineItems(cartId as string, items)
      break
    }

    case 'PUT': {
      cart = await shopifyClient.checkout.updateLineItems(cartId as string, items)
      break
    }

    case 'DELETE': {
      cart = await shopifyClient.checkout.removeLineItems(cartId as string, items)
      break
    }

    default: {
      throw new Error('Invalid method!')
    }
  }

  res.status(200).json(cart)
}

export default handler
