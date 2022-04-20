import type { Cart, LineItemToAdd } from 'shopify-buy'
import type { VALID_METHODS } from './index.page'
import { apiAxios } from 'Config/api-configuration'

const performLineItemRequest = async (
  method: typeof VALID_METHODS[0],
  cartId: string,
  items: LineItemToAdd[]
): Promise<Cart> => {
  const { data } = await apiAxios({
    url: 'shopify/checkout/line-items',
    method,
    data: { items },
    params: {
      cart_id: cartId,
    },
  })
  return data
}

export const addLineItems = async (cartId: string, items: LineItemToAdd[]) =>
  await performLineItemRequest('POST', cartId, items)

export const updateLineItems = async (cartId: string, items: LineItemToAdd[]) =>
  await performLineItemRequest('PUT', cartId, items)

export const removeLineItems = async (cartId: string, items: LineItemToAdd[]) =>
  await performLineItemRequest('DELETE', cartId, items)
