import { Client } from 'Config/prismic-configuration'

function linkResolver (doc) {
  // Pretty URLs for known types
  if (doc.type === 'blog') {
    return `/blog/${doc.uid}`
  }

  if (doc.type === 'blogad') {
    return `/transformations/${doc.uid}`
  }

  if (doc.type === 'shop_landing') {
    return '/store'
  }

  if (doc.type === 'personalised_page') {
    return `/custom/${doc.uid}`
  }

  // Fallback for other types, in case new custom types get created
  return `/${doc.uid}`
}

export default async (req, res) => {
  const { token: ref, documentId } = req.query
  const redirectUrl = await Client(req).getPreviewResolver(ref, documentId).resolve(linkResolver, '/')

  if (!redirectUrl) {
    return res.status(401).json({ message: 'Invalid token' })
  }

  res.setPreviewData({ ref })
  // Redirect the user to the share endpoint from same origin. This is
  // necessary due to a Chrome bug:
  // https://bugs.chromium.org/p/chromium/issues/detail?id=696204
  res.write(
    `<!DOCTYPE html><html><head><meta http-equiv="Refresh" content="0; url=${redirectUrl}" />
    <script>window.location.href = '${redirectUrl}'</script>
    </head>`
  )
  res.end()
}
