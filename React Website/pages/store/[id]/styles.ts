import styled from 'styled-components'

export const PageStyle = styled.div`
  padding: 130px 5%;

  > div {
    padding: 0 10%;
  }

  h1 {
    text-transform: capitalize;
    font-size: 2rem;
  }

  table {
    width: 100% !important;
  }

  .input,
  label {
    display: inline-block;
    margin-bottom: 0.5rem;
  }

  .swiper-button-prev,
  .swiper-button-next {
    color: ${(p) => p.theme.colors.secondary};

    &:after {
      font: normal normal normal 1.2rem FontAwesome;
      content: "\f054";
    }
  }

  .swiper-button-prev {
    &:after {
      content: "\f053";
    }
  }

  .button {
    background: ${(p) => p.theme.colors.secondary};

    &:hover {
      background: ${(p) => p.theme.colors.secondary};
    }
  }

  .available-on {
    font-weight: bold;
    margin-top: 0.25rem;
  }

  .add-form {
    margin-top: 2rem;
  }
`

export const ProductGrid = styled.section`
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr;
  list-style: none;
  column-gap: 2rem;
  row-gap: 4rem;

  img {
    width: 100%;
    height: auto;
  }

  .slider-container {
    width: 100%;
  }

  .swiper-slide {
    width: 100% !important;
  }

  .item img {
    padding-top: 0px !important;
  }

  .controls {
    position: absolute;
    bottom: 0;
    width: 100%;
  }

  @media (min-width: 901px) {
    grid-template-columns: repeat(2, 1fr);

    .details {
      padding: 0 2rem 0 12rem;
    }
  }
`

export const Price = styled.span`
  display: inline-block;
  margin-bottom: 2rem;
  font-size: 2rem;
  font-weight: 700;
`

export const ProductControls = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 1rem;
`

export const AccordianBase = styled.section`
  margin-top: 3rem;

  .fa-chevron-down {
    display: block;
  }

  .fa-chevron-up {
    display: none;
  }

  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-size: 1rem;
  }

  > header {
    padding: 0.5rem 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #c6c6c8;
    margin-bottom: 1rem;

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      margin: 0;
    }
  }

  > div {
    display: none;
  }

  &.active {
    > div {
      display: block;
    }

    .fa-chevron-down {
      display: none;
    }

    .fa-chevron-up {
      display: block;
    }
  }
`
