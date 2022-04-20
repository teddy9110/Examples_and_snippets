import styled from 'styled-components'

export const InputContainer = styled.div`
  position: relative;

  .message.error {
    margin-top: 0.4rem;
    font-size: 0.9rem;
    color: ${(p) => p.theme.colors.primary};
  }
`

export const Label = styled.label`
  color: ${(p) => p.theme.colors.secondary};
  font-weight: bold;
  font-size: 1rem;
  letter-spacing: 0.04rem;
`

export const Wrapper = styled.input`
  all: unset;
  width: 100%;
  box-sizing: border-box;
  height: 42px;
  display: flex;
  align-items: center;
  padding: 0 1rem;
  border-width: 1px;
  border-style: solid;
  border-color: #c7c7c7;
  transition: all 0.3s;

  &[type="file"] {
    padding: 0.6rem 1rem;
  }

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
