import styled from 'styled-components'

export const PageStyle = styled.div`
  padding: 80px 0;

  > div {
    padding: 0 5%;
  }

  h1 {
    text-align: center;
    font-size: 2rem;
  }

  @media (min-width: 901px) {
    padding: 100px 0;
  }
`

export const BannerLink = styled.a`
  display: inline-block;
  width: 100%;

  img {
    width: 100%;
  }
`

export const CollectionList = styled.section`
  display: grid;
  grid-template-columns: 1fr;
  column-gap: 1rem;
  row-gap: 1rem;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  h3 {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    height: 118px;
    color: ${(p) => p.theme.colors.secondary};
  }

  span {
    border-bottom: 1px solid ${(p) => p.theme.colors.secondary};
    font-weight: normal;
  }

  a {
    max-height: 118px;
    overflow: hidden;
    border-radius: 0.5rem;
    display: grid;
    background: #e8e8e8;
    grid-template-columns: 1fr 1fr;
    text-align: center;
    margin-bottom: 1rem;
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};

    .img {
      display: flex;
      height: 100%;
      overflow: hidden;
      justify-content: flex-end;
    }
  }

  @media (min-width: 901px) {
    grid-template-columns: 1fr 1fr 1fr;
  }
`
