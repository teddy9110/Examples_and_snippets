import styled from 'styled-components'

export const CareerContainer = styled.article`
  text-align: center;
  padding: 4rem 0;

  .button {
    margin: 0 auto 2rem auto;
  }

  .item > article {
    padding-top: 0px;
    margin-top: 50px;
  }

  .list {
    max-width: 400px;
    margin: 0 auto;
    padding: 0 10%;
  }
`

export const CareerItem = styled.a`
  display: grid;
  grid-template-columns: 2fr 1fr;
  text-decoration: none;
  color: ${(p) => p.theme.colors.secondary};
  padding: 1rem 0;
  border-bottom: 1px solid #c6c6c8;

  &:first-of-type {
    border-top: 1px solid #c6c6c8;
  }

  h3 {
    font-size: 1rem;
    margin-bottom: 0.3rem;
  }

  span {
    font-weight: normal;
    font-size: 0.9rem;
  }

  section {
    font-weight: bold;
    text-align: left;
  }

  i {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
`
