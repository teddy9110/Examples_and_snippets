import React from 'react'
import Dialog from 'Components/Dialog'
import Button from 'Components/primitives/form/Button'
import AddToCartForm from 'Components/AddToCartForm'
import btoa from 'btoa'
import { RichText } from 'prismic-reactjs'
import { ProductUpsell } from './styles'

interface product {
  variants: any[]
  image: {
    src: string
    alt: string
  }
}

interface Props {
  control: (object: object) => void
  title: string
  open: boolean
  data: {
    open: boolean
    title: string
    description: any
    product?: product
  }
}

const PromotedDialog = (props: Props) => {
  const { data, control, open, title } = props
  return (
    <Dialog control={control} open={open} title={title}>
      <ProductUpsell>
        <img
          src={data.product?.image.src}
          alt={data.product?.image.alt}
          loading="lazy"
        />
        {RichText.render(data.description)}
        <AddToCartForm>
          <input
            type="hidden"
            name="variantId"
            value={btoa(data.product?.variants[0].admin_graphql_api_id || '')}
          />
          <input type="hidden" name="quantity" value={1} />
          <Button type="submit">Add To Cart</Button>
        </AddToCartForm>
      </ProductUpsell>
    </Dialog>
  )
}

export default PromotedDialog
