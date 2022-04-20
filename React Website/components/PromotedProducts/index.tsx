import React from 'react'
import ProductGrid from 'Components/ProductGrid'
import ProductCard from 'Components/ProductCard'
import { Container } from './styles'

interface Props {
  items: any[]
}

const PromotedProducts: React.FC<Props> = (props) => {
  const { items } = props
  return (
    <Container>
      <h2>You may also like</h2>
      <ProductGrid className="noPadding">
        {items.map(({ item, staging_id: stagingId }) => (
          <ProductCard
            key={item.id}
            availableForSale={true}
            handle={item.handle}
            title={item.title}
            images={item.images}
            variants={item.variants}
            stagingId={stagingId}
          />
        ))}
      </ProductGrid>
    </Container>
  )
}

export default PromotedProducts
