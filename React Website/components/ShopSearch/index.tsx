import React from 'react'
import Input from 'Components/primitives/form/inputs/Input'
import Button from 'Components/primitives/form/Button'
import { useRouter } from 'next/router'
import { Filters } from './styles'

const CollectionFilters: React.FC = () => {
  const router = useRouter()

  const searchProducts = (e) => {
    e.preventDefault()
    router.push(`/store/search?q=${e.target.search.value}`)
      .catch((e) => console.log(e))
  }

  return (
    <Filters>
      <section className="items"></section>
      <form onSubmit={searchProducts}>
        <Input placeholder="Search here..." name="search" id="search" />
        <Button type="submit">
          <i className="fa fa-search" aria-hidden="true"></i>Search
        </Button>
      </form>
    </Filters>
  )
}

export default CollectionFilters
