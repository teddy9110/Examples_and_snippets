import styled from 'styled-components'

export const RichTextContent = styled.div`
  @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');
  @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

  .ql-font-roboto {
    font-family: 'Roboto', sans-serif;
  }

  .ql-font-poppins {
    font-family: 'Poppins', sans-serif;
  }

  font-family: "Poppins", sans-serif !important;

  p, div {
    display: block !important;
    line-height: initial !important;
  }

  h1, h2, h3, h4, h5, h6 {
    font-weight: bold !important;
  }

  h1 {
    font-size: 32px !important;
  }

  h2 {
    font-size: 28px !important;
  }

  h3 {
    font-size: 24px !important;
  }

  h4 {
    font-size: 22px !important;
  }

  h5 {
    font-size: 20px !important;
  }

  h6 {
    font-size: 16px !important;
  }

  p {
    margin-top: 10px !important;
  }

  .ql-align-left {
    text-align: left;
  }

  .ql-align-center {
    text-align: center;
  }

  .ql-align-right {
    text-align: right;
  }
`
