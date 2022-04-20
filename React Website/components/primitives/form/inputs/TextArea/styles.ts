import styled from 'styled-components'

export const InputContainer = styled.div`
  position: relative;
  margin-bottom: 1rem;

  .message.error {
    margin-top: 0.4rem;
    font-size: 0.9rem;
    color: ${(p) => p.theme.colors.primary};
  }

  .red {
    color: red;
  }
`

export const Label = styled.label`
  color: ${(p) => p.theme.colors.secondary};
  font-weight: bold;
  font-size: 1rem;
  letter-spacing: 0.04rem;
`

export const Wrapper = styled.textarea`
  all: unset;
  width: 100%;
  box-sizing: border-box;
  height: 48px;
  display: block;
  padding: 1rem;
  margin-top: 0.3rem;
  border-width: 2px;
  border-style: solid;
  border-color: ${(p) => p.theme.colors.secondary};
  transition: all 0.3s;
  min-height: 300px;

  &:focus:optional {
    box-shadow: 0 0 0 2px #355dff;
  }

  &:focus:required {
    box-shadow: 0 0 0 2px red;
  }

  &:focus:invalid {
    box-shadow: 0 0 0 2px red;
  }

  &:required:focus:valid {
    box-shadow: 0 0 0 2px #4ebf66;
  }
`
