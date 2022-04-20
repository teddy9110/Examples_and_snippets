import styled from 'styled-components'

export const Wrapper = styled.article`
  text-align: center;
  padding: 1rem 5%;

  .arrow {
    top: 0;
    z-index: 99;
  }

  @media (min-width: 901px) {
    .swiper-pagination-bullet-active {
      display: none;
    }
  }
`
