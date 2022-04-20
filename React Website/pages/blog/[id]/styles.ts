import StrapiImage from 'Components/image/StrapiImage'
import styled from 'styled-components'

export const PageStyle = styled.div`
  padding: 74px 0 0;

  > div {
    padding: 0 10%;
  }

  h1 {
    font-size: 2rem;
    margin-bottom: 0;
  }

  @media (min-width: 804px) {
    padding: 88px 0 0;
  }

  @media (min-width: 901px) {
    padding: 70px 0 0;

    > div {
      padding: 0 25%;
    }

    h1 {
      font-size: 3.5rem;
    }

    h3 {
      font-size: 2rem;
    }
  }
`

export const Share = styled.div`
  margin: 1rem 0;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  text-align: center;
  justify-content: center;

  i {
    font-size: 1.5rem;
    margin: 0 1rem;
  }

  @media (min-width: 901px) {
    position: absolute;
    top: 0;
    left: 15%;
    flex-direction: column;

    i {
      font-size: 1.5rem;
      margin: 0.5rem 0;
    }
  }
`

export const TransformationEmbed = styled.div`
  display: flex;
  justify-content: center;
`

export const ShareTitle = styled.h2`
  font-size: 1.5rem;
  min-width: 100%;

  @media (min-width: 901px) {
    font-size: 0.9rem;
    min-width: 100%;
  }
`

export const BlogTitle = styled.h2`
  color: ${(p) => p.theme.colors.secondary_text};
  font-size: 1.5rem;

  @media (min-width: 901px) {
    font-size: 2rem;
  }
`

export const HeaderImage = styled(StrapiImage)`
  margin: auto;
  max-height: 50vh !important;
  object-fit: contain !important;

  @media (min-width: 901px) {
    max-height: 40vh !important;
    width: auto !important;
  }
`
