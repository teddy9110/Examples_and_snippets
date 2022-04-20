interface CollectionSortOptions {
  sortBy?: string
  limit?: number
  imageLimit?: number
  variantLimit?: number
}

export const collectionSort = (collectionName: string, options: CollectionSortOptions = {}) => {
  const { sortBy, limit = 200, imageLimit = 20, variantLimit = 20 } = options
  const filter = sortBy ? sortBy.split('-') : []

  const productArgs = Object.entries({
    first: limit,
    sortKey: filter.length >= 2 ? filter[0].toUpperCase() : null,
    reverse: filter.length >= 2 ? filter[1] : null,
  }).filter(([_, val]) => val)
    .map(([key, value]) => `${key}: ${value}`)
    .join(', ')

  return `
    {
      collectionByHandle(handle: "${collectionName}") {
        products(${productArgs}) {
          edges {
            node {
              id
              handle
              title
              description
              tags
              availableForSale
              images(first: ${imageLimit}) {
                edges {
                  node {
                    src
                    altText
                  }
                }
              }
              variants(first: ${variantLimit}) {
                edges {
                  node {
                    id
                    price
                    availableForSale
                    sku
                  }
                }
              }
            }
          }
        }
      }
    }
  `
}

export const query = (query: string) => {
  return `
    {
      products(first: 200, query: "${query}") {
        edges {
          node {
            id
            handle
            title
            description
            availableForSale,
            images(first:20) {
              edges {
                node {
                  src
                  altText
                }
              }
            }
            variants(first: 20) {
              edges {
                node {
                  id
                  price
                  availableForSale
                  sku
                }
              }
            }
          }
        }
      }  
    }
  `
}

interface ProductByHandleOptions {
  imageLimit?: number
  optionsLimit?: number
  variantLimit?: number
}

export const productByHandle = (handle: string, options: ProductByHandleOptions = {}) => {
  const { imageLimit = 20, optionsLimit = 20, variantLimit = 20 } = options

  return `
    {
      productByHandle(handle: "${handle}") {
        id
        handle
        title
        description
        tags
        descriptionHtml
        availableForSale
        images(first: ${imageLimit}) {
          edges {
            node {
              src
              altText
            }
          }
        }
        options(first: ${optionsLimit}){
          name
          id
        }
        variants(first: ${variantLimit}) {
          edges {
            node {
              id
              price
              title
              availableForSale
              sku
            }
          }
        }
      }
    }
  `
}
