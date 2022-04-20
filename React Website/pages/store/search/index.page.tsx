import React, { useEffect } from 'react'
import ProductStore from 'Store/products'
import { useRouter } from 'next/router'
import SearchItem from 'Components/SearchItem'
import Button from 'Components/primitives/form/Button'
import Input from 'Components/primitives/form/inputs/Input'
import { List, PageStyle } from './styles'

const Search = () => {
  const router = useRouter()

  const { searchProducts, searchedProducts, loading } = ProductStore(
    (state) => ({
      loading: state.loading,
      searchedProducts: state.searchedProducts,
      searchProducts: state.searchProducts,
    })
  )

  useEffect(() => {
    searchProducts(router.query.q as string)
      .catch((e) => console.log(e))
  }, [router])

  const searchProductsSubmit = (e) => {
    e.preventDefault()
    router.push(`/store/search?q=${e.target.search.value}`)
      .catch((e) => console.log(e))
  }

  return (
    <PageStyle>
      <h1>Your search for “{router.query.q}” revealed the following:</h1>
      <form onSubmit={searchProductsSubmit}>
        <Input placeholder="search here..." name="search" id="search" />
        <Button type="submit">Search</Button>
      </form>
      {loading ? 'loading' : null}
      <List>
        {searchedProducts.map((item) => (
          <SearchItem
            key={item.id}
            handle={item.handle}
            title={item.title}
            images={item.images}
            variants={item.variants}
            description={item.description}
          />
        ))}
      </List>
    </PageStyle>
  )
}

export default Search
