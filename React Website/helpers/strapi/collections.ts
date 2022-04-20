import type { StrapiAttributes, StrapiModel, StrapiOptions } from '.'
import { strapiAxiosInstance } from 'Config/strapi'

export type StrapiCollectionFilterOperations =
  '$eq' |
  '$ne' |
  '$lt' |
  '$lte' |
  '$gt' |
  '$gte' |
  '$in' |
  '$notIn' |
  '$contains' |
  '$notContains' |
  '$containsi' |
  '$notContainsi' |
  '$null' |
  '$notNull' |
  '$between' |
  '$startsWith' |
  '$endsWith'

export interface StrapiCollectionFilter {
  field: string
  operation: StrapiCollectionFilterOperations
  value: string
}

type SortValues <T> = string & keyof (T & StrapiAttributes)

export interface StrapiCollectionSort <A> {
  key?: string
  values: (`${SortValues<A>}:asc` | `${SortValues<A>}:desc`)[]
}

export interface StrapiCollectionPaginationOptions {
  page?: number
  limit?: number
}

export interface StrapiCollectionPagination {
  page: number
  pageSize: number
  pageCount: number
  total: number
}

export interface StrapiCollectionOptions <A> extends StrapiOptions <A> {
  filter?: StrapiCollectionFilter[]
  sort?: StrapiCollectionSort<A>[]
  pagination?: StrapiCollectionPaginationOptions
}

export type StrapiCollection <A> = StrapiModel<A>[] & {
  pagination: StrapiCollectionPagination
}

export const getCollectionItems = async <A> (
  collectionName: string,
  options: StrapiCollectionOptions<A> = {}
): Promise<StrapiCollection<A>> => {
  const { filter = [], sort = [], populate = [] } = options
  let queryStrings: string[] = []

  queryStrings = queryStrings.concat(filter.map(
    ({ field, operation, value }) => `filters${field.toString()}[${operation}]=${value}`
  ))

  queryStrings = queryStrings.concat(sort.map(
    ({ key, values }) => !key ? `sort=${values.join(',')}` : `sort${key}=${values.join(',')}`
  ))

  queryStrings = queryStrings.concat(populate.map(
    ({ key, value }) => !key ? `populate=${value.toString()}` : `populate${key}=${value.toString()}`
  ))

  if (options.pagination) {
    const { page = 1, limit = 100 } = options.pagination
    queryStrings.push(`pagination[page]=${page}`)
    queryStrings.push(`pagination[pageSize]=${limit}`)
  }

  const { data: { data, meta } } = await strapiAxiosInstance(`${collectionName}?${queryStrings.join('&')}`)

  Object.defineProperty(data, 'pagination', {
    // eslint-disable-next-line @typescript-eslint/consistent-type-assertions
    value: {
      page: meta.pagination.page,
      pageSize: meta.pagination.pageSize,
      pageCount: meta.pagination.pageCount,
      total: meta.pagination.total,
    } as StrapiCollectionPagination,
    enumerable: false,
  })

  return data
}

export const findCollectionItem = async <A> (
  collectionName: string,
  id: string,
  options: StrapiOptions<A> = {}
): Promise<StrapiModel<A>> => {
  const { populate = [] } = options
  let queryStrings: string[] = []

  queryStrings = queryStrings.concat(populate.map(
    ({ key, value }) => !key ? `populate=${value.toString()}` : `populate${key}=${value.toString()}`
  ))

  const { data: { data } } = await strapiAxiosInstance(`${collectionName}/${id}?${queryStrings.join('&')}`)
  return data
}
