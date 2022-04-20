import styled from 'styled-components'

export const Pagination = styled.div`
  padding: 2.2% 10% !important;  
  margin: 0;
  display: flex;
  justify-content: center;
`

export const PageNumberContainer = styled.div`
  float: left;
`

export const PageNumber = styled.span`
  text-align: center !important;
  padding: 0 0.8rem !important;
  font-size: 1.5rem;
  cursor: pointer;
  
  &:hover {
    color: #d61334 !important;
  }

  &.active {
    border-bottom: 3px solid #d61334;

    .dark {
      display:block;
    }

    .light {
      display:none;
    }
  }
`

export const PageChangeLink = styled.i`
  cursor: pointer !important;
  font-size: 1.2rem !important;
  padding: 0 0.8rem !important;
`
