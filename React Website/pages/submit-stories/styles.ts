import styled from 'styled-components'
import PageBanner from 'Components/banners/PageBanner'

export const PageStyle = styled.div`
  padding: 3rem 5%;
  background: #fafafe;

  .input,
  .select {
    margin-bottom: 1rem;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 0;
  }

  @media (min-width: 901px) {
    padding: 3rem 30%;
  }
`

export const PageHeader = styled(PageBanner)`
  margin: 70px 0 0;
  padding: 0 1rem;
  height: 301px;

  h1 {
    font-size: 3rem;
    margin-bottom: 0;
  }
`

export const ErrorSpan = styled.span`
  color: red;
  display: block;
  margin: 1rem 0;
  font-weight: bold;
`

export const SuccessSpan = styled.span`
  color: #17C68B;
  display: block;
  margin: 1rem 0;
  font-weight: bold;
`
