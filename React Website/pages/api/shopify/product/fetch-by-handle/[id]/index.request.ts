import type { Product } from 'shopify-buy'
import { apiAxios } from 'Config/api-configuration'

const fetchByHandle = async (id: string): Promise<Product> => {
  const { data } = await apiAxios.get(`shopify/product/fetch-by-handle/${id}`)
  return data
}

export default fetchByHandle
