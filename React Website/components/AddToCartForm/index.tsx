import React, { ReactNode } from 'react'
import useForm from 'Hooks/useForm'
import { Client as PClient } from 'Config/prismic-configuration'
import { localShopifyClient } from 'Config/shopify-configuration'
import cartStore from 'Store/cartStore'

interface Props {
  children: ReactNode
  setLoading?: (value: boolean) => void
  setPromotionDialog?: (item: any) => void
}

const AddToCartForm = (props: Props) => {
  const { children, setLoading = () => {}, setPromotionDialog } = props
  const { loadCart } = cartStore((state) => ({
    loadCart: state.loadCart,
  }))

  const hasPromotedItem = async (sku) => {
    try {
      return await PClient().getByUID('cart_promotion', sku, {})
    } catch (e) {
      return false
    }
  }

  const addToCard = async (data) => {
    setLoading(true)

    const item = {
      variantId: data.variantId,
      quantity: parseInt(data.quantity),
    }

    const promotedItem = await hasPromotedItem(data.sku)

    if (promotedItem) {
      setPromotionDialog({
        title: promotedItem.data.title[0].text,
        description: promotedItem.data.description,
        product: promotedItem.data.upsell_product,
        open: true,
      })
    }

    const cartId = localStorage.getItem('cart_id')

    if (!cartId) {
      const cart = await localShopifyClient.checkout.create()
      localStorage.setItem('cart_id', cart.id.toString())

      try {
        await localShopifyClient.checkout.addLineItems(cart.id.toString(), [item])

        if (!promotedItem) {
          location.replace('/store/cart')
        } else {
          await loadCart()
        }
      } catch (e) {
        setLoading(false)
      }
    } else {
      try {
        await localShopifyClient.checkout.addLineItems(cartId, [item])
        const promotedItem = await hasPromotedItem(data.sku)

        if (!promotedItem) {
          location.replace('/store/cart')
        } else {
          await loadCart()
        }
      } catch (e) {
        setLoading(false)
        localStorage.removeItem('cart_id')
      }
    }
  }

  const [submitAction] = useForm(addToCard as any)

  return (
    <>
      <form onSubmit={submitAction}>{children}</form>
    </>
  )
}

export default AddToCartForm
