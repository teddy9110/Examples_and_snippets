import styled from 'styled-components'

export const Address = styled.div`
  padding: 1.7rem;
  
  @media (min-width: 901px) {
    display: flex;
    justify-content: space-between;
    width: 50%;
    margin: 0 auto;
  }
  
  @media (min-width: 1200px) {
    display: flex;
    justify-content: space-around;
    width: 50%;
    margin: 0 auto;
  }
`

export const AddressSection = styled.section`
  font-size: 1.125rem;
  color: ${(p) => p.theme.colors.primary_text};
  
  article {
    line-height: 1.33;
    padding: 0 1rem;
  }
  
  :first-child {
    margin-bottom: 1.75rem;
  }
  
  h4 {
    font-size: 1.5rem;
    text-align: center;
    font-weight: 700;
    font-stretch: normal;
    line-height: 1.33;
    letter-spacing: -0.02px;
  }
  
  a {
    font-size:1.125rem;
    line-height: 1.44;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    text-decoration: none;
  }

  @media (min-width: 901px) {
    h4 {
      text-align: left;
    }
    
    article {
      padding: 0;
    }
    
    :first-child {
      margin-bottom: 0;
    }
  }
`
