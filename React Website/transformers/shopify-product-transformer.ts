const transfromShopifyProducts = (data: any[]) => {
  const toSensibleArray = data.map((item) => {
    const tags: string[] = item.node.tags

    const preorderTag = (tags || [])
      .find((item) => item.startsWith('preorder'))

    const availableOn = !preorderTag
      ? null
      : preorderTag.split('_')[1]

    return {
      handle: item.node.handle,
      title: item.node.title,
      availableForSale: item.node.availableForSale,
      description: item.node.description,
      tags: item.node.tags,
      isPreorder: !!preorderTag,
      availableOn,
      images: item.node.images.edges.map(({ node: { src, altText } }) => ({
        src,
        altText,
      })),
      variants: item.node.variants.edges.map(({ node: { id, price, availableForSale, sku } }) => ({
        id,
        price,
        availableForSale,
        sku,
      })),
    }
  })

  return toSensibleArray
}

export default transfromShopifyProducts
