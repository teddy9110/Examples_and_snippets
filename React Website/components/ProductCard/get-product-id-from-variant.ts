const getProductIdFromVariant = (variant: any, stagingId?: string) => {
  if (process.env.NODE_ENV !== 'production' && stagingId) {
    return `gid://shopify/ProductVariant/${stagingId}`
  }

  return variant.admin_graphql_api_id || variant.id
}

export default getProductIdFromVariant
