import { Strapi, StrapiModel } from 'Helpers/strapi'
import { StrapiCollectionOptions } from 'Helpers/strapi/collections'

export interface BlogCategoryAttributes {
  slug: string
  name: string
  order: number
}

export type BlogCategory = StrapiModel<BlogCategoryAttributes>

export const getBlogCategories = async (
  options: StrapiCollectionOptions<BlogCategoryAttributes> = {}
) => await Strapi.collections.all<BlogCategoryAttributes>('blog-categories', {
  sort: [
    {
      values: ['order:asc'],
    },
  ],
  ...options,
})

export const findBlogCategory = async (
  slug: string,
  options: StrapiCollectionOptions<BlogCategoryAttributes> = {}
) => {
  const post = await Strapi.collections.find<BlogCategoryAttributes>('blog-categories', slug, {
    ...options,
  })

  return post
}
