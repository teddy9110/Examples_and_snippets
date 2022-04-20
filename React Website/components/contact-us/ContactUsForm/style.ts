import styled from 'styled-components'

export const Contact = styled.div`
  font-family: 'Poppins', sans-serif;
  padding: 1.7rem;
  
  h3 {
    font-size: 2rem;
    line-height: 1.19;
    letter-spacing: normal;
    font-stretch: normal;
    font-style: normal;
    text-align: center;
    font-weight: 700;
  }
  
  article {
    font-size: 1.025rem;
    font-weight: normal;
    font-stretch: normal;
    font-style: normal;
    line-height: 1.44;
    text-align: center;
  }
  
  form {
    display: flex;
    flex-direction: column;
    padding-top: 1rem;
    
    label {
      font-size: 1.025rem;
      font-weight: 500;
      line-height: 0.72;
      padding: 0.6rem 0;
    }
    
    input {
      background-color: #fff;
      border: solid 2px ${(p) => p.theme.colors.primary_text};
      border-radius: 2px;
      margin-bottom: 1rem;
    }
    
    select {
      -webkit-appearance: none;
      --moz-appearance: none;
      background-color: #fff;
      background: url('data:image/svg+xml;utf-8,<svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px"><path d="M24 24H0V0h24v24z" fill="none" opacity=".87"/><path d="M16.59 8.59L12 13.17 7.41 8.59 6 10l6 6 6-6-1.41-1.41z"/></svg>') 100% 50% no-repeat transparent;
      background-position: top 10px right 4px;
      border: solid 2px ${(p) => p.theme.colors.primary_text};
      border-radius: 2px;
      margin-bottom: 1rem;
    }

    input#attachments {
      background-color: #fff;
      border: dashed 2px ${(p) => p.theme.colors.primary_text};
      border-radius: 2px;
      padding: 0.8rem 0.4rem;
    }
    
    textarea {
      border: solid 2px ${(p) => p.theme.colors.primary_text};
      border-radius: 2px;
      padding: 0.6rem 0.2rem;
      resize: none;
    }

    input, select, textarea {
      padding: 0.8rem 0.4rem;
      font-size: 1.025rem;

      &::placeholder {
        font-size: 1.025rem;
      }
    }
  }

  @media (min-width: 901px)  {
    display: flex;
    flex-direction: column;
    justify-content: center;
    margin: 0 auto;
    width: 50%;
  }
`

export const FormComplete = styled.div`
  display: flex;
  justify-content: center;
  flex-direction: column;
  margin: 1.5rem auto;
  text-align: center;
  
  h3 {
    font-size: 1.5rem;
  }
  
  p {
    font-size: 0.9rem;
  }
  
  i {
    margin-top: 1.3rem;
  }
  
  @media(min-width: 901px) {
    width: 50%;
    
    h3 {
      line-height: 1;
    }
    
    p {
      line-height: 1
    }

  }
`
