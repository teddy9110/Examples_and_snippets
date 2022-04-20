import { apiAxios } from 'Config/api-configuration'

const fetchSorted = async (collectionName: string, sortBy?: string): Promise<any[]> => {
  const { data } = await apiAxios.get('shopify/product/fetch-sorted', {
    params: {
      collection_name: collectionName,
      sort_by: sortBy,
    },
  })

  return data
}

export default fetchSorted
