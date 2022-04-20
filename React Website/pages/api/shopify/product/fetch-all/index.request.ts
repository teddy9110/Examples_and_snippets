import type { Product } from 'shopify-buy'
import { apiAxios } from 'Config/api-configuration'

const fetchAll = async (pageSize?: number): Promise<Product[]> => {
  const { data } = await apiAxios.get('shopify/product/fetch-all', {
    params: {
      pageSize,
    },
  })

  return data
}

export default fetchAll
