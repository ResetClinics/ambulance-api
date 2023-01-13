export const setList = () => {
  const result = []
  // eslint-disable-next-line no-plusplus
  for (let i = 0; i < 50; i++) {
    result.push({ name: `Услуга-${i}`, id: i })
  }
  return result
}
