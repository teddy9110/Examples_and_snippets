import { apiAxios } from 'Config/api-configuration'

const searchProducts = async (query: string): Promise<any[]> => {
  const { data } = await apiAxios.get('shopify/product/search', {
    params: { query },
  })

  return data
}

export default searchProducts
