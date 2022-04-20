import styled from 'styled-components'

export const Wrapper = styled.li`
  border-bottom: 1px solid #e8e8e8;

  img {
    width: 100%;
    height: auto;
    object-fit: contain;
  }

  section {
    padding: 1rem 0;
    display: flex;
    flex-direction: column;

    > * {
      flex: 1;
    }
  }

  h3 {
    font-weight: 500;
    font-size: 1.2rem;
    margin-bottom: 0.3rem;
    overflow: hidden;
  }

  a {
    display: grid;
    grid-template-columns: 160px 1fr;
    text-decoration: none;
    color: ${(p) => p.theme.colors.secondary};
  }

  p {
    font-weight: normal;
    text-transform: lowercase;
  }
`

export const ReducedPrice = styled.span`
  del {
    font-weight: normal;
    color: rgb(129 129 129);
    text-decoration: none;
    position: relative;
    margin-right: 0.5em;

    &:before {
      content: " ";
      display: block;
      width: 100%;
      border-top: 2px solid rgb(129 129 129);
      height: 11px;
      position: absolute;
      bottom: 0;
      left: 0;
    }
  }
`

export const PriceDiffrence = styled.span`
  color: red;
  margin-left 0.2em;
  font-weight: normal;
`
