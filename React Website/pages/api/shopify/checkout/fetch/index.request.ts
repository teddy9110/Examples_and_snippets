import type { Cart } from 'shopify-buy'
import { apiAxios } from 'Config/api-configuration'

const fetchCart = async (cartId: string): Promise<Cart> => {
  const { data } = await apiAxios.get('shopify/checkout/fetch', {
    params: {
      cart_id: cartId,
    },
  })
  return data
}

export default fetchCart
