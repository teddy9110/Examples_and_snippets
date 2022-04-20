import styled from 'styled-components'

export const BlogList = styled.ul`
  padding: 0;
  margin: 4rem 0 0 0;
  display: grid;
  grid-template-columns: 1fr;
  list-style: none;
  column-gap: 2rem;

  li {
    display: none;

    &:first-child {
      display: block;
    }
  }

  @media (min-width: 901px) {
    grid-template-columns: 1fr 1fr 1fr;

    li {
      display: block;
    }
  }
`

const BlogCard = styled.article`
  flex: 1;
  text-align: left;

  a {
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};
  }

  img {
    width: 100%;
    height: auto;
  }

  .category {
    display: inline-block;
    margin: 1rem 0;
    text-transform: uppercase;
    font-size: 1.2rem;
    color: ${(p) => p.theme.colors.secondary};
    font-weight: 500;
  }

  h3 {
    font-size: 1.5rem;
  }

  p {
    font-weight: normal;
  }

  @media (min-width: 901px) {
    grid-template-columns: 1fr 1fr 1fr;

    img {
      object-fit: contain;
      height: 300px;
    }
  }
`

export default BlogCard
