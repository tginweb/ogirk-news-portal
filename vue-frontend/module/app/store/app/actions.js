export function setMenuItems(context, data) {
  context.commit('setMenuItems', data);
}

export async function playerPlayPostAudio(context, {item, mode, comId}) {

  const requestParams = {
    postId: item.nid,
    mode: mode
  }

  try {
    const res = await this.$apiWp.get('smart-speech/post-audio-download', {params: requestParams})

    if (res.data && res.data.url) {
      const fileUrl = res.data.url
      context.dispatch('dialogShow', ['app/player', {
        itemTitle: item.title,
        itemUrl: item.url,
        sourceComId: comId,
        source: {
          format: 'audio',
          provider: 'audio',
          file: fileUrl
        },
        time: 0,
        playing: true,
        mode: 'minimized',
      }], {root:true})
    }
  } catch (e) {

    console.log(e)
  }


}

export function playerOpenVideo(context, data) {
  context.dispatch('dialogShow', ['app/player', {
    sourceComId: data.comId,
    source: {
      format: 'audio',
      provider: 'audio',
      file: data.fileUrl
    },
    time: 0,
    playing: true,
    mode: 'minimized',
  }])
}
