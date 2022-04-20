export const transformData = (inputData, pageType) => {
  let usableData = {}
  // build up the data , as there are 2 styles that can come in
  if (pageType) {
    usableData = inputData.items?.map((item) => {
      const tempData = {
        image: item.icon,
        role: null,
        name: item.title?.[0].text,
        body: item.detail?.[0].text,
      }
      return tempData
    })
  } else {
    usableData = inputData.results?.slice(0, 4).map((item) => {
      const tempData = {
        image: item.data.image,
        role: item.data.role,
        name: item.data.name,
        body: item.data.body,
      }
      return tempData
    })
  }
  return usableData
}
