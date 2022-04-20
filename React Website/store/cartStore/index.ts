import type { Cart, LineItem } from 'shopify-buy'
import { localShopifyClient } from 'Config/shopify-configuration'
import create, { State } from 'zustand'

interface CustomCart extends Cart {
  webUrl: string
  totalPrice: number
}

interface CartStore extends State {
  loading: boolean
  itemLoading: boolean
  deleteLoading: boolean
  total: number | null
  checkoutURL: string | null
  items: LineItem[]
  promotion_items: any[]
  loadCart: () => Promise<any>
  removeItem: (data: any) => Promise<any>
  updateItem: (data: any) => Promise<any>
}

const cartStore = create <CartStore>((set, get) => ({
  loading: true,
  itemLoading: false,
  deleteLoading: false,
  total: null,
  checkoutURL: null,
  items: [],
  promotion_items: [],

  async loadCart () {
    set({ loading: true })
    const cartId = localStorage.getItem('cart_id')

    if (cartId) {
      try {
        const cart = await localShopifyClient.checkout.fetch(cartId) as CustomCart

        // Do something with the checkout
        if (cart.completedAt) {
          localStorage.removeItem('cart_id')
          return await get().loadCart()
        }

        set({
          checkoutURL: cart.webUrl,
          loading: false,
          items: cart.lineItems,
          total: cart.totalPrice,
        })

        return cart
      } catch (e) {}
    } else {
      const cart = await localShopifyClient.checkout.create() as CustomCart
      localStorage.setItem('cart_id', cart.id.toString())

      set({
        checkoutURL: cart.webUrl,
        loading: false,
        items: cart.lineItems,
        total: cart.totalPrice,
      })

      return cart
    }
  },
  async removeItem (item) {
    set({ deleteLoading: true })

    try {
      const cartId = localStorage.getItem('cart_id')
      const cart = await localShopifyClient.checkout.removeLineItems(cartId, [item])

      set({ deleteLoading: false })
      await get().loadCart()

      return cart
    } catch (e) {}
  },
  async updateItem (data) {
    set({ itemLoading: true })

    try {
      const cartId = localStorage.getItem('cart_id')
      const cart = await localShopifyClient.checkout.updateLineItems(cartId, [data]) as CustomCart

      set({ total: cart.totalPrice })

      return cart
    } catch (e) {}
  },
}))

export default cartStore
