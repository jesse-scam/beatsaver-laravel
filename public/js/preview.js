/**
 * @typedef {Object} SongInfo
 * @property {string} songName
 * @property {string} artistName
 * @property {string} authorName
 * @property {number} beatsPerMinute
 * @property {number} previewStartTime
 * @property {number} previewDuration
 */

class PreviewPlayer {
  constructor (volume) {
    this.audio = new Audio()
    this.audio.volume = volume || 0.5

    /**
     * @type {Map<string, Blob>}
     */
    this.blobStore = new Map()

    this.onEnd = () => {}
    this.audio.addEventListener('ended', ev => {
      this.onEnd(ev)
    })
  }

  /**
   * @param {string} url URL
   * @returns {Promise.<Blob>}
   */
  static async _fetchBlob (url) {
    const resp = await fetch(url)
    return resp.blob()
  }

  /**
   * @param {Blob} blob Blob
   * @returns {Promise.<zipReader>}
   */
  static _createReader (blob) {
    return new Promise((resolve, reject) => {
      zip.createReader(
        new zip.BlobReader(blob),
        zipReader => { resolve(zipReader) }
      )
    })
  }

  /**
   * @param {zipReader} zipReader Zip Reader
   * @returns {Promise.<SongInfo>}
   */
  static _getSongInfo (zipReader) {
    return new Promise((resolve, reject) => {
      zipReader.getEntries(entries => {
        const infoJSON = entries.find(x => x.filename.includes('SongInfo.json'))

        infoJSON.getData(new zip.TextWriter(), text => {
          const json = JSON.parse(text)
          resolve(json)
        })
      })
    })
  }

  /**
   * @param {zipReader} zipReader Zip Reader
   * @param {string} audioExtension Audio Path
   * @returns {Promise.<Blob>}
   */
  static _getSongBlob (zipReader, audioExtension) {
    return new Promise((resolve, reject) => {
      zipReader.getEntries(entries => {
        const song = entries.find(x => x.filename.includes(audioExtension))

        song.getData(
          new zip.BlobWriter(),
          blob => { resolve(blob) }
        )
      })
    })
  }

  get playing () { return !this.audio.paused }
  stop () { this.audio.pause() }

  /**
   * @param {string} key Song Key
   * @returns {Promise.<SongInfo>}
   */
  async play (key) {
    const cache = this.blobStore.get(key)
    if (cache) {
      const { song, info } = cache
      return this._play(song, info)
    }

    const [id] = key.split('-')
    const url = `/storage/songs/${id}/${key}.zip`

    const blob = await PreviewPlayer._fetchBlob(url)
    const zipReader = await PreviewPlayer._createReader(blob)

    const info = await PreviewPlayer._getSongInfo(zipReader)
    const audioExtension = '.ogg';

    const songBlob = await PreviewPlayer._getSongBlob(zipReader, audioExtension)
    const song = URL.createObjectURL(songBlob)

    this.blobStore.set(key, { song, info })
    return this._play(song, info)
  }

  _play (song, info) {
    this.audio.pause()
    this.audio.src = song
    this.audio.play()

    return info
  }
}
