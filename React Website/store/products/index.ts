import create, { State } from 'zustand'
import { localShopifyClient } from 'Config/shopify-configuration'

interface ProductStore extends State {
  loading: boolean
  filteredProducts: any[]
  searchedProducts: any[]
  filterProducts: (collection_name: string, sort_by: string) => Promise<any>
  searchProducts: (search_query: string) => Promise<any>
}

const productStore = create <ProductStore>((set, get) => ({
  loading: false,
  filteredProducts: [],
  searchedProducts: [],
  filterProducts: async (collectionName, sortBy) => {
    set({
      filteredProducts: await localShopifyClient.product.fetchSorted(collectionName, sortBy),
    })
  },
  searchProducts: async (searchQuery) => {
    set({ loading: true })
    set({
      searchedProducts: await localShopifyClient.product.search(searchQuery),
      loading: false,
    })
  },
}))

export default productStore
