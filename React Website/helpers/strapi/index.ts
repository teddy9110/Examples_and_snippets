import { strapiAxiosInstance } from 'Config/strapi'
import { getCollectionItems, findCollectionItem } from './collections'

export interface StrapiAttributes {
  createdAt: string
  updatedAt: string
}

export interface StrapiModel <A> {
  id: number
  attributes: A & StrapiAttributes
}

export type StrapiRelation <A> = A & {
  id: number
}

interface StrapiImageFormat {
  name: string
  hash: string
  ext: string
  mime: string
  width: number
  height: number
  size: number
  path?: string
  url: string
}

export type StrapiImage = DataWrapper < StrapiModel <{
  name: string
  alternativeText: string
  caption: string
  width: number
  height: number
  formats?: {
    thumbnail?: StrapiImageFormat
    large?: StrapiImageFormat
    medium?: StrapiImageFormat
    small?: StrapiImageFormat
  }
  hash: string
  ext: string
  mime: string
  size: number
  url: string
  previewUrl?: string
  provider: string
  provider_metadata?: any
  createdAt: string
  updatedAt: string
}>>

export type StrapiImageFormatKeys = keyof StrapiImage['data']['attributes']['formats']

export interface StrapiPopulate <A> {
  key?: `[${string & keyof A}][populate]`
  value: keyof A | '*'
}

export interface StrapiOptions <A> {
  populate?: StrapiPopulate<A>[]
}

const Strapi = {
  instance: strapiAxiosInstance,
  collections: {
    all: getCollectionItems,
    find: findCollectionItem,
  },
}

export { Strapi }
