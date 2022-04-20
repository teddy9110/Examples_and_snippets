import styled from 'styled-components'
import BlogCard from 'Components/BlogCard'
import PageBanner from 'Components/banners/PageBanner'

export const PageStyle = styled.div`
  > div {
    padding: 0 10%;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 0;
  }
`

export const PageHeader = styled(PageBanner)`
  padding: 0 1rem;  
  margin: 70px 0 0;
  text-align: center;
  height: 301px;

  h1 {
    min-width: 100%;
    font-size: 3rem;
    margin-bottom: 0;
  }

  @media(min-width:901px) {
    h1 {
      min-width: inherit;
    }
  }
`

export const BlogList = styled.ul`
  padding: 0 10%;
  margin: 0;
  display: grid;
  list-style: none;
  column-gap: 3rem;
  row-gap: 3rem;

  @media (min-width: 901px) {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
`

export const FeaturedBlogPost = styled(BlogCard)`
  list-style: none;
  img {
    max-height: 500px;
    height: 100%;
    width: 100%;
    object-fit: cover;
  }

  @media (min-width: 1303px) {
    position: relative;
    overflow: hidden;

    a {
      > section {
        background: #fff;
        width: 40%;
        padding: 2rem;
        position: absolute;
        top: 2rem;
        left: 2rem;
      }
    }
  }
`

export const FeaturedHeader = styled.div`
  margin: 2rem 0;
  grid-template-columns: 1fr;

  .newsletter {
    border: 4px solid ${(p) => p.theme.colors.secondary};
  }

  @media (min-width: 1303px) {
    display: grid;
    column-gap: 3.1rem;
    row-gap: 1rem;
    list-style: none;
    grid-template-columns: 65.1% 1fr;
  }
`

export const Filters = styled.section`
  padding: 1rem 10%;
  margin-bottom: 2rem;
  border-bottom: 1px solid #e8e8e8;

  a {
    display: inline-block;
    padding: 0.5rem 0;
    margin: 0 1rem;
    text-decoration: none;
    text-transform: capitalize;
    color: #000;
    font-weight: bold;
    order: 1;
  }

  .input {
    min-width: 100%;
    margin-bottom: 0;
  }

  @media (min-width: 901px) {
    display: flex;
    justify-content: flex-end;

    .select {
      min-width: 200px;
    }
  }
`

export const NewsLetter = styled.div`
  height: 100%;
  overflow: hidden;
  box-sizing: border-box;
  background: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 2rem;

  .button {
    width: 100%;
  }

  iframe {
    position: relative;
    z-index: 0;
    margin-top: -40%;
    width: 120% !important;
    margin-left: -10%;
  }

  .small {
    position: relative;
    z-index: 1;
    top: -10%;
  }

  i {
    position: relative;
    z-index: 1;
    font-size: 2rem;
    margin-bottom: 0.3rem;
  }

  h3 {
    position: relative;
    z-index: 1;
    font-size: 1.4rem;
  }

  @media (min-width: 400px) {
    iframe {
      position: relative;
      z-index: 0;
      margin-top: 0%;
      width: 120% !important;
      margin-left: -10%;
    }
  }

  @media (min-width: 1301px) {
    iframe {
      position: relative;
      z-index: 0;
      margin-top: -20%;
      width: 120% !important;
      margin-left: -10%;
    }
  }
`

interface FilterLinkProps {
  order?: number
}

export const FilterLink = styled.a<FilterLinkProps>`
  order: ${(p) => (p.order >= 0 ? p.order : 1)} !important;

  &.active {
    border-bottom: 3px solid ${(p) => p.theme.colors.primary};
  }
`
