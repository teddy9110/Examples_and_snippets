import CenteredContainer from 'Components/containers/CenteredContainer'
import ContentContainer from 'Components/containers/ContentContainer'
import styled from 'styled-components'

export const PricingContainer = styled(ContentContainer)`
  margin: 0 auto;
  min-width: 260px;
  display: flex;
  gap: 0;
  flex-direction: column;
  align-items: baseline;
  padding: 0 0 2rem;

  section {
    margin: 0 auto;
  }

  > div {
    width: 100%;
    flex: 1;
    height: 100%;
    margin: 0 auto;
  }

  .price {
    color: ${(p) => p.theme.colors.primary};
    font-size: 2.5rem;
    font-weight: bold;
    line-height: 100%;
  }

  .smallPrice {
    color: #000;
    font-size: 1rem;
  }

  .question {
    display: flex;
    margin-left: 0.5rem;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    font-style: normal;
    height: 20px;
    width: 20px;
    color: #fff;
    background: ${(p) => p.theme.colors.secondary};
    border-radius: 1000px;

    &:hover {
      cursor: pointer;
    }
  }

  h3 {
    font-size: 2rem;
    font-weight: 800;
    color: #000;
  }

  input {
    display: none;
  }

  i {
    color: #15c78c;
  }

  ul {
    display: flex;
    justify-content: space-between;
    margin: 1rem 0 1rem 0;
    padding: 0;
    flex-direction: column;

    li {
      display: flex;
    }
  }

  a {
    color: #fff;
    font-size: 0.9rem;
  }

  .toggle {
    background: #fff;
    display: flex;
    padding: 1rem;
    align-items: center;
    justify-content: center;

    label {
      font-weight: 500;
      border: 2px solid ${(p) => p.theme.colors.secondary};
      display: flex;
      padding: 0.5rem 1rem;
    }
  }

  .plan {
    background: #fff;
    display: none;
    align-items: center;
    flex-direction: column;

    > section {
      min-width: 100%;
      box-sizing: border-box;
      padding: 0 2rem 2rem 2rem;
      margin: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    ul {
      margin: 0.5rem 0;
      justify-content: center;

      li {
        justify-content: center;
        font-weight: 500;
        font-size: 0.9rem;
        margin: 0 0 0.5rem 0;
      }

      p {
        margin: 0;
      }
    }
  }

  strong {
    color: ${(p) => p.theme.colors.primary} !important;
  }

  input#monthly:checked ~ .plans .monthly,
  input#annually:checked ~ .plans .annually {
    display: flex;
  }

  input#annually:checked ~ .toggle .annually,
  input#monthly:checked ~ .toggle .monthly {
    background: ${(p) => p.theme.colors.secondary};
    color: #fff;
  }
  color: ${(p) => p.theme.colors.secondary};

  @media (min-width: 901px) {
    padding: 0;
    .list ul {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    .toggle {
      display: none;
    }

    .plan {
      display: flex;
      flex: 1;
    }

    .plan-container {
      max-width: 700px;
      margin: 0 auto;
    }

    .plans {
      padding-top: 2rem;
      width: 100%;
      background: #fff;
      align-items: center;
      justify-content: center;
      flex-direction: row;
      display: flex;
    }

    > div {
      > header {
        border-top: 0;
      }
    }
  }
`

export const PricingPanel = styled(CenteredContainer)`
  padding: 2rem 1rem 0;
  background: none;

  .large-view {
    display: none;
    margin-bottom: 2rem;
  }

  .small-view {
    width: 100%;
    box-sizing: border-box;
  }

  section {
    width: 100%;
    margin: 0 auto;
  }

  @media (min-width: 768px) {
    section {
      width: 60%;
    }

    .large-view {
      display: block;
      padding: 0 10%;
    }

    .small-view {
      display: none;
    }
  }

  @media (min-width: 901px) {
    section {
      width: 70%;
    }
  }
`

export const ProductUpsell = styled.div`
  img {
    max-width: 50%;
    margin: 0 auto;
    display: block;
  }

  .button {
    min-width: 100%;
  }
`

export const SliceTitle = styled.div`
  color: #fff;
  padding: 0 0 1rem;
  text-align: center;

  h3 {
    color: #1a2238;
  }

  @media (min-width: 901px) {
    padding: 1.8rem 0;
  }

`

export const Carousel = styled.div`
  scroll-snap-type: x mandatory;

  .slides {
    scroll-snap-align: start;
    -webkit-overflow-scrolling: touch;
    display: flex;
    overflow-x: scroll;
  }


  @keyframes toNext {
    75% {
      left: 0;
    }
    95% {
      left: 100%;
    }
    98% {
      left: 100%;
    }

    99% {
      left: 0;
    }
  }

  @keyframes toStart {
      75% {
        left: 0;
      }
      95% {
        left: -300%;
      }
      98% {
        left: -300%;
      }
      99% {
        left: 0;
      }
    }

    @keyframes snap {
      96% {
        scroll-snap-align: center;
      }
      97% {
        scroll-snap-align: none;
      }
      99% {
        scroll-snap-align: none;
      }
      100% {
        scroll-snap-align: center;
      }
    }
`
