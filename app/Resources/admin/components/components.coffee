through = require "through2"
vfs = require "vinyl-fs"

Components = ->
  search = (bundle, callback) ->
    append = (file, enc, cb) ->
      component = file.relative.split(".")[0]
      bundle.require require.resolve(file.path), expose: "Engage360d/#{component}"
      cb()

    complete = (cb) =>
      @push bundle
      @emit "end"
      cb()

    vfs.src(["#{__dirname}/**/**/**/**/**.coffee"])
      .pipe through.obj append, complete

  through.obj search

module.exports = Components
