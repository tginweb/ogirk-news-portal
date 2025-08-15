export function adZonesBySlug(state) {
  return state.adZones.reduce((map, obj) => (map[obj.slug] = obj, map), {});
}

export function teaserZonesByQuery(state) {

  return state.adTeaserZones.reduce((map, term) => {

    if (term.meta.zone_queries) {
      term.meta.zone_queries.forEach((item) => {

        if (!map[item.query]) {
          map[item.query] = {}
        }

        map[item.query][item.position] = {
          slug: term.slug,
          nid: term.nid,
          name: term.name,
        }
      })
    }

    return map
  }, {});
}
