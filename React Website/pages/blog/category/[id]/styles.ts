import styled from 'styled-components'
import PageBanner from 'Components/banners/PageBanner'

export const PageStyle = styled.div`
  margin: 0 0 0;

  > div {
    padding: 0 10%;
  }

  h1 {
    font-size: 2rem;
    text-transform: capitalize;
    margin-bottom: 0;
  }
`

export const PageHeader = styled(PageBanner)`
  padding: 80px 1rem 0 1rem;  
  text-align: center;
  height: 101px;

  h1 {
    font-size: 3rem;
    margin-bottom: 0;
    text-transform: capitalize;
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
    grid-template-columns: repeat(3, 1fr);
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
    top: -10%;
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
`

interface FilterLinkProps {
  order?: number
}

export const FilterLink = styled.a<FilterLinkProps>`
  order: ${(p) => (p.order >= 0 ? p.order : 1)}!important;
  display: inline-block;
  padding-bottom: 0.5rem;

  &.active {
    border-bottom: 3px solid ${(p) => p.theme.colors.primary};
  }
`
