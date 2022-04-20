import React from 'react'
import axios from 'axios'

interface Props {
  terms: any
}

const Index = ({ terms }: Props) => {
  return (
    <div style={{ margin: '6rem auto', width: '80%' }}>
      <div dangerouslySetInnerHTML={{ __html: terms }} />
    </div>
  )
}

export default Index

export async function getServerSideProps () {
  let terms: any

  try {
    const { data } = await axios.get('https://api.teamrhfitness.com/tcs')
    terms = data
  } catch (e) {
    console.log(e)
  }

  return {
    props: {
      terms,
    },
  }
}
