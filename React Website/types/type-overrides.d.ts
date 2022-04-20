import 'swiper/react'

// Add missing ref property from swiper module
declare module 'swiper/react' {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  export interface Swiper<P = any> {
    ref?: MutableRefObject<any>
  }
}

declare module 'shopify-buy' {
  export interface CollectionResource {
    // eslint-disable-next-line @typescript-eslint/method-signature-style
    fetchAllWithProducts(options?: { first?: number, productsFirst?: number }): Promise<Collection[]>
  }
}
