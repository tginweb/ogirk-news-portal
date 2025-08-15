export function findMenuPath(menu, url) {

  const scanTree = (items, parents) => {

    let foundPath, foundChildrenPath;

    for (let i = 0; i < items.length; i++) {

      let path = [...parents],
        found = false

      const item = items[i]

      path.push(item)

      if (item.url == url) {
        found = true
      }

      if (item.children) {
        foundChildrenPath = scanTree(item.children, path)

        if (foundChildrenPath) {
          path = [...foundChildrenPath]
          found = true
        }
      }

      if (found) {
        foundPath = [...path]
      }
    }

    return foundPath
  }

  return scanTree(menu, [])
}
