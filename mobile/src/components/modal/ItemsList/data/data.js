export const getItems = () => {
  const result = []
  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < 50; i++) {
    result.push({ name: `Медикамент-${i}`, id: i })
  }
  return result
}
