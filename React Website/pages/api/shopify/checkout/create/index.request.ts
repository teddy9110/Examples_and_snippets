import type { Cart } from 'shopify-buy'
import { apiAxios } from 'Config/api-configuration'

const createCart = async (): Promise<Cart> => {
  const { data } = await apiAxios.post('shopify/checkout/create')
  return data
}

export default createCart
