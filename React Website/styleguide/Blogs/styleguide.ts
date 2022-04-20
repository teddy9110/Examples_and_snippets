import styled from 'styled-components'

export const BlogHeader = styled.header`
  display: grid;
  grid-template-columns: 1;
  background: ${(p) => p.theme.colors.space_cadette_blue};
  margin-bottom: 2rem;
  h1 {
    font-size: 2rem;
  }
  .category {
    display: inline-block;
    margin: 1rem 0;
    text-transform: uppercase;
    font-size: 1.2rem;
    color: ${(p) => p.theme.colors.secondary};
    font-weight: 500;
  }
  .title {
    padding: 0rem 2rem;
    display: flex;
    justify-content: center;
    flex-direction: column;
  }
  .image {
    img {
      box-sizing: border-box;
      width: 100%;
      padding: 0 2rem 2rem 2rem;
      display: block;
      height: 100%;
      object-fit: cover;
    }
  }
  @media (min-width: 901px) {
    h1 {
      font-size: 3rem;
    }
    .title {
      padding: 4rem 2rem;
    }
    grid-template-columns: 2fr 1.5fr;
    .title {
      padding: 4rem 4rem 4rem 10%;
    }
    .image img {
      padding: 0;
    }
  }
`

export const Body = styled.div`
  position: relative;
  strong {
    color: ${(p) => p.theme.colors.secondary};
  }
  img {
    width: 100%;
    height: auto;
  }
  @media (min-width: 901px) {
    ul {
      line-height: 2.1rem;
      font-size: 1rem;
    }
  }
`
