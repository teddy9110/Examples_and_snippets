import styled from 'styled-components'

const PriceCard = styled.article`
  min-width: 200px;
  max-width: 389px;
  width: 100%;
  margin: 0 auto;
  border-radius: 0.5rem;
  overflow: hidden;
  text-align: center;
  background: ${(p) => p.theme.colors.primary_bg};
  color: ${(p) => p.theme.colors.secondary};
  margin-bottom: 1rem;

  header {
    height: 60px;
    display: flex;
    font-weight: bold;
    align-items: center;
    justify-content: center;
    background: ${(p) => p.theme.colors.tertiary};
    color: ${(p) => p.theme.colors.secondary};
    text-transform: uppercase;
  }

  > div {
    padding: 2rem;
  }

  ul {
    padding: 0;
    margin: 0.5rem 0;
    list-style: none;

    li {
      margin: 0.5rem 0;
    }
  }

  h3 {
    margin-bottom: 0.5rem;
  }

  .text-cta {
    color: ${(p) => p.theme.colors.secondary};
    border-color: ${(p) => p.theme.colors.secondary};
  }

  .price {
    display: inline-block;
    color: ${(p) => p.theme.colors.primary};
    font-weight: bold;
    font-size: 2rem;
    margin-bottom: 1rem;
  }

  a {
    color: ${(p) => p.theme.colors.secondary};
  }

  .button {
    background: ${(p) => p.theme.colors.secondary};
    color: #fff;
    margin: 1rem auto;

    &:hover {
      color: ${(p) => p.theme.colors.secondary};
    }
  }
`

export default PriceCard
