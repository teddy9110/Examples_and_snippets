import styled from 'styled-components'

const blackBackgroundImage = () =>
  'data:image/svg+xml,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 4 5\'%3e%3cpath fill=\'%23343a40\' d=\'M2 0L0 2h4zm0 5L0 3h4z\'/%3e%3c/svg%3e'

export const InputContainer = styled.div`
  position: relative;
`

export const Label = styled.label`
  color: ${(p) => p.theme.colors.secondary};
  font-weight: bold;
  font-size: 1rem;
  letter-spacing: 0.04rem;
`

export const SelectInput = styled.select`
  all: unset;
  width: 100%;
  box-sizing: border-box;
  height: 42px;
  align-items: center;
  padding: 0 1rem;
  line-height: 25px;
  text-transform: capitalize;
  border-width: 1px;
  border-style: solid;
  border-color: #c7c7c7;
  transition: all 0.3s;
  background: url("${blackBackgroundImage}") no-repeat right 0.75rem center / 8px 10px;
  display: inline-block;
  vertical-align: middle;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  color: #787878;

  option {
    color: #000;
    -webkit-text-fill-color: #000;
    text-transform: capitalize;
  }
`
