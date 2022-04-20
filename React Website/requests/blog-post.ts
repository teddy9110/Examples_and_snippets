import type { BlogCategoryAttributes } from './blog-category'

import { Strapi, StrapiImage, StrapiModel, StrapiPopulate, StrapiRelation } from 'Helpers/strapi'
import { StrapiCollectionOptions } from 'Helpers/strapi/collections'
import { StrapiMetadata } from './metadata'

export interface BlogPostAttributes {
  slug: string
  title: string
  contentHtml: string
  featuredImage: StrapiImage
  metadata: StrapiMetadata
  category?: StrapiRelation<BlogCategoryAttributes>
}

export type BlogPost = StrapiModel<BlogPostAttributes>

const POPULATE: StrapiPopulate<BlogPostAttributes>[] = [
  {
    key: '[featuredImage][populate]',
    value: '*',
  },
  {
    key: '[metadata][populate]',
    value: '*',
  },
  {
    key: '[category][populate]',
    value: '*',
  },
]

export const getBlogPosts = async (
  options: StrapiCollectionOptions<BlogPostAttributes> = {}
) => await Strapi.collections.all<BlogPostAttributes>(
  'blog-posts', {
    populate: POPULATE,
    sort: [
      {
        values: ['createdAt:desc'],
      },
    ],
    ...options,
  }
)

export const findBlogPost = async (
  slug: string,
  options: StrapiCollectionOptions<BlogPostAttributes> = {}
) => {
  const post = await Strapi.collections.find<BlogPostAttributes>('blog-posts', slug, {
    populate: POPULATE,
    ...options,
  })

  return post
}
