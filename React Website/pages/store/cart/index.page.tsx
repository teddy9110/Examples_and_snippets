import React, { useState } from 'react'
import Input from 'Components/primitives/form/inputs/Input'
import Button from 'Components/primitives/form/Button'
import cartStore from 'Store/cartStore'
import CheckBox from 'Components/primitives/form/inputs/Checkbox'
import PromotedProducts from 'Components/PromotedProducts'
import Prismic from 'prismic-javascript'
import { Client as PClient } from 'Config/prismic-configuration'
import { CartItems, CartLayout, CartSummary, Loader, PageHeader, PageStyle } from './styles'

const Index = (props: any) => {
  const { promotedProducts } = props
  const [agreed, setAgreed] = useState(true)

  const { loading, items, checkoutURL, updateItem, removeItem, total } =
    cartStore((state) => ({
      loading: state.loading,
      checkoutURL: state.checkoutURL,
      updateItem: state.updateItem,
      removeItem: state.removeItem,
      total: state.total,
      items: state.items,
    }))

  const redirect = () => location.replace(checkoutURL)

  const hasLifePlan = items.filter(
    (item: any) => item.variant.sku === '10000001' || item.variant.sku === '10000002' || item.variant.sku === '10000076' || item.variant.sku === '10000082'
  )

  const updateCartItem = (e) => {
    if (parseInt(e.target.value) === 0) return
    updateItem({
      id: e.target.id,
      quantity: parseInt(e.target.value),
    }).catch((e) => console.log(e))
  }

  if (loading) {
    return (
      <PageStyle>
        <PageHeader>
          <h1>Shopping Bag</h1>
        </PageHeader>
        <Loader>
          <i className="fa fa-spinner" aria-hidden="true"></i>
        </Loader>
      </PageStyle>
    )
  }

  return (
    <PageStyle>
      <PageHeader>
        <h1>Shopping Bag</h1>
      </PageHeader>
      <CartLayout>
        {!loading && items.length === 0
          ? (
            <ul>No Items</ul>
          )
          : (
            <>
              <ul style={{ padding: 0, margin: '0 0 2rem 0' }}>
                {items.map((item: any) => (
                  <CartItems
                    key={item.variant.image.src}
                  >
                    <section>
                      <img
                        loading="lazy"
                        height="131px"
                        width="131px"
                        style={{ objectFit: 'cover' }}
                        src={item.variant.image.src}
                        alt={item.variant.altText}
                      />
                    </section>
                    <section className="details">
                      <h3>{item.title}</h3>
                      <span>£{item.variant.price}</span>
                      <form>
                        <Input
                          style={{ width: '86px', height: '41px' }}
                          onChange={updateCartItem}
                          id={item.id}
                          min="1"
                          type="number"
                          label="quantity"
                          name="quantity"
                          defaultValue={item.quantity}
                        ></Input>
                      </form>
                    </section>
                    <form
                      onSubmit={(e: any) => {
                        e.preventDefault()
                        removeItem(e.target.lineItem.value).catch((e) => console.log(e))
                      }}
                    >
                      <input name="lineItem" type="hidden" value={item.id} />
                      <button type="submit" className="remove">
                        <i className="fa fa-times" aria-hidden="true"></i>
                      </button>
                    </form>
                  </CartItems>
                ))}
              </ul>
              <section>
                <CartSummary>
                  <h4>Order Summary</h4>
                  <div>
                    <div>
                      <strong>Discount</strong>
                      <span>Apply at checkout</span>
                    </div>
                    <div>
                      <strong>Delivery</strong>
                      <span>Confirm at checkout</span>
                    </div>
                    <div>
                      <strong>Total</strong>
                      <span>£{total}</span>
                    </div>
                  </div>
                  <hr />
                  {hasLifePlan.length > 0
                    ? (
                      <CheckBox
                        name="marketing agreement"
                        className="small-label"
                        onChange={(e) => {
                          if (e.target.checked === true) {
                            setAgreed(false)
                          } else {
                            setAgreed(true)
                          }
                        }}
                        label={
                          <>
                        I give express consent to receive digital content from
                        Team RH Fitness immediately when purchasing the Life
                        Plan. I understand that this means I am not entitled to
                        a 14-day cooling-off period. I also agree to the{' '}
                            <a href="/terms">terms</a> and conditions and{' '}
                            <a href="/privacy-policy">privacy policy</a>.
                          </>
                        }
                      />
                    )
                    : null}

                  <Button
                    disabled={hasLifePlan.length > 0 ? agreed : false}
                    onClick={redirect}
                  >
                  Checkout
                  </Button>
                  <div className="continue">
                    <a href="/store">Continue Shopping</a>
                  </div>
                </CartSummary>
              </section>
            </>
          )}
      </CartLayout>
      <PromotedProducts items={promotedProducts} />
    </PageStyle>
  )
}

export async function getStaticProps (context) {
  try {
    const promotedProducts = await PClient().query(
      Prismic.Predicates.at('document.type', 'promoted_products')
    )

    return {
      props: {
        promotedProducts: promotedProducts.results[0].data.products,
      },
      revalidate: 1,
    }
  } catch (e) {
    return { props: { promotedProducts: {} }, revalidate: 60 }
  }
}

export default Index
