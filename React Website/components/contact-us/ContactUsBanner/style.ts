import styled from 'styled-components'

export const Banner = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  
  font-family: 'Poppins', sans-serif;
  background-color: ${({ theme }) => theme.colors.secondary};
  height: 500px;
  padding: 1.7rem;
  
  @media (min-width: 901px) {
    
    flex-direction: row;
    height: 450px;
    padding: 5rem;
  }
`

export const BannerInner = styled.div`
  display: flex;
  flex-direction: column;
  color: #fff;
  align-items: center;
  
  img {
    max-width:80%;
  }
  
  picture {
    display: flex;
    justify-content: center;
  }
  
  @media (min-width: 768px) {
    img {
      max-width: 50%;
    }
  }
  
  @media (min-width: 901px) {
    display: flex;
    justify-content: center;
    picture {
     padding: 0;
    }
    
    img {
      max-width: 55%;
    }
    
    :first-child {
      padding: 2rem;
    }
    
    :last-child {
      display: flex;
    }
  }

  @media (min-width: 1990px) {
    img {
      max-width: 50%;
    }
  }
  
  @media (min-width: 2390px) {
    img {
      max-width: 40%;
    }
  }
`

export const BannerHeader = styled.div`
  padding-top: 0.5rem;
  padding-bottom: 1.5rem;
  
  h2 {
    text-align: center;
    font-size: 2rem;
  }
  
  span {
    font-size: 1.075rem;
    line-height: 1.44;
  }
  @media (min-width: 901px) {
    h2 {
      font-size: 3rem;
      text-align: left;
    }
    
    span {
      font-size: 1.2rem;
      line-height: 1.56;
    }
  }
  
`
