import Prismic from 'prismic-javascript'

export const apiEndpoint = `https://${process.env.NEXT_PUBLIC_PRISMIC_REPO ?? ''}.cdn.prismic.io/api/v2`
export const accessToken = ''

// Client method to query documents from the Prismic repo
export const Client = (req = null) =>
  Prismic.client(apiEndpoint, createClientOptions(req, accessToken))

const createClientOptions = (req = null, prismicAccessToken = null) => {
  const reqOption = req ? { req } : {}

  const accessTokenOption = prismicAccessToken
    ? { accessToken: prismicAccessToken }
    : {}

  return {
    ...reqOption,
    ...accessTokenOption,
  }
}
