import type { Collection } from 'shopify-buy'
import { apiAxios } from 'Config/api-configuration'

interface Options {
  first?: number
  productsFirst?: number
}

const fetchAllWithProducts = async ({ first, productsFirst }: Options = {}): Promise<Collection[]> => {
  const { data } = await apiAxios.get('shopify/collection/fetch-all-with-products', {
    params: {
      first,
      products_first: productsFirst,
    },
  })

  return data
}

export default fetchAllWithProducts
