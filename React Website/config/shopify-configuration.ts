import Client from 'shopify-buy'
import axios from 'axios'

import {
  addLineItems,
  updateLineItems,
  removeLineItems,
} from 'Pages/api/shopify/checkout/line-items/index.request'

import fetchByHandle from 'Pages/api/shopify/product/fetch-by-handle/[id]/index.request'
import fetchAll from 'Pages/api/shopify/product/fetch-all/index.request'
import fetchAllWithProducts from 'Pages/api/shopify/collection/fetch-all-with-products/index.request'
import createCart from 'Pages/api/shopify/checkout/create/index.request'
import fetchCart from 'Pages/api/shopify/checkout/fetch/index.request'
import fetchSorted from 'Pages/api/shopify/product/fetch-sorted/index.request'
import searchProducts from 'Pages/api/shopify/product/search/index.request'

export const shopifyAxios = axios.create({
  baseURL: `https://${process.env.STOREFRONT_DOMAIN}/api`,
  headers: {
    'X-Shopify-Storefront-Access-Token': process.env.STOREFRONT_TOKEN,
    'Content-Type': 'application/graphql',
    Accept: 'application/json',
  },
})

const shopifyClient = Client.buildClient({
  domain: process.env.STOREFRONT_DOMAIN!,
  storefrontAccessToken: process.env.STOREFRONT_TOKEN!,
})

export const localShopifyClient = {
  product: {
    fetchByHandle,
    fetchAll,
    fetchSorted,
    search: searchProducts,
  },
  collection: {
    fetchAllWithProducts,
  },
  checkout: {
    create: createCart,
    addLineItems,
    updateLineItems,
    removeLineItems,
    fetch: fetchCart,
  },
}

export default shopifyClient
