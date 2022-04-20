import styled from 'styled-components'

export const ComparisonsContainer = styled.div`
  margin-top: 2rem;

  .large-view {
    display: none;
  }

  .small-view {
    width: 100%;
    box-sizing: border-box;
  }

  > h2 {
    text-align: center;
    font-size: 2rem;
    padding: 0 10%;
    margin: 2rem 0;
  }

  h3 {
    font-size: 2rem;
    color: #000;
    text-align: center;
    padding: 0 1.125rem;
  }

  .disclaimer {
    font-size: 14px;
    display: block;
    text-align: center;
    color: #777A87;
  }

  @media (min-width: 901px) {
    .large-view {
      display: block;
      padding: 0 10%;
    }

    .small-view {
      display: none;
    }

    h3 {
      font-size: 2rem;
      line-height: 2.5rem;
      color: #000;
      margin-bottom: 2rem;
    }
  }
`

export const ComparisonsContent = styled.div`
  margin-bottom: 3rem;
`
