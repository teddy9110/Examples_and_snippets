import styled from 'styled-components'

export const Wrapper = styled.article`
  background: #fef7f1;
  text-align: center;
  padding: 1rem 5%;

  .lead {
    h1 {
      font-size: 3rem !important;
    }

    @media (min-width: 901px) {
      h1 {
        font-size: 4rem !important;
      }
    }
  }

  @media (min-width: 901px) {
    h1 {
      font-size: 1.2rem !important;
    }

    .lead {
      padding-bottom: 1rem;
    }

    > section {
      margin: 0 auto 1rem auto;
      max-width: 946px;
    }
  }
`

export const Details = styled.section`
  background: #fff;
  text-align: left;
  margin-bottom: 1rem;
  box-shadow: 0 5px 5px rgba(0, 0, 0, 0.1);

  > header {
    list-style: none;
    padding: 0.5rem 2rem;
    display: grid;
    grid-template-columns: 80px 1fr 80px;
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    .controls {
      font-size: 1.2rem;
      margin: 0;
      display: flex;
      align-items: center;
    }
  }

  .controls {
    justify-content: flex-end;
  }

  > summary::-webkit-details-marker {
    display: none;
  }

  > div {
    transition: max-height 0.3s;
    box-sizing: border-box;
    max-height: 0;
    overflow: hidden;

    > div {
      padding: 1rem 2rem 2rem 2rem;
    }
  }

  &.active > div {
    max-height: 1000px;
    overflow-y: auto;
  }
`
