import React from 'react'
import { Address, AddressSection } from './style'

const ContactUsAddresses = () => {
  return (
    <Address>
      <AddressSection>
        <h4>In person:</h4>
        <article>Team RH Fitness Ltd <br/>
          Unit 11 Portobello Trade Park <br/>
          Portobello Road <br/>
          Birtley <br/>
          Chester Le Street <br/>
          DH32SB
        </article>
      </AddressSection>
      <AddressSection>
        <h4>By Email:</h4>
        <article>
          <a href="mailto:enquiries@teamrhfitness.com">enquiries@teamrhfitness.com</a>
        </article>
      </AddressSection>
    </Address>
  )
}

export default ContactUsAddresses
