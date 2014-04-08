args   = require("optimist").argv
gulp = require "gulp"
gutil = require "gulp-util"
gulpif = require "gulp-if"
rename = require "gulp-rename"
shim = require "browserify-shim"
browserify = require "browserify"
coffeeify = require "coffeeify"
reactify = require "reactify"
uglify = require "gulp-uglify"
jade = require "gulp-jade"
stylus = require "gulp-stylus"
concat = require "gulp-concat"
through = require "through2"
path = require "path"

components = require "./components/components"
isProduction = args.type is "production"
target = path.resolve args.target or "./../../../web/bundles"
pkg = require "./package.json"

gulp.task "scripts", ->
  search = (file, encoding, callback) ->
    bundle.require file.path, expose: file.relative.split(".")[0]
    callback()

  complete = ->
    @push bundle
    @emit "end"

  resolveShim = (shim) ->
    for lib of shim
      shim[lib].path = path.resolve shim[lib].path
    shim

  bundle = shim browserify(), resolveShim pkg.shim
  gulp.src(["./pages/**.coffee", "./admin.coffee"], read: false)
    .pipe(through.obj search, complete)
    .pipe(components())
    .pipe(through.obj (bundle, enc, callback) ->
      bundle.transform "coffeeify"
      bundle.transform "reactify"
      bundle.bundle (err, content) =>
        return callback err if err
        @push new gutil.File
          base: target
          cwd: target
          path: "#{target}/admin.js"
          contents: new Buffer content
        @emit "end"
    )
    .pipe(gulp.dest(target))

gulp.task "views", ->
  gulp.src("./admin.jade")
    .pipe(jade())
    .pipe(gulp.dest("#{target}/../"))

gulp.task "styles", ->
  gulp.src("./components/**/**/**/*.styl")
    .pipe(
      stylus(
        use: ["nib"], 
        set: ["include css", true],
        import:[],
        paths:[
          "./node_modules/"
        ]
      )
    )
    .pipe(concat("admin.css"))
    .pipe(gulp.dest(target))

gulp.task "watch", ->
  gulp.watch "./admin.jade", ["views"]
  gulp.watch "./**/*.coffee", ["scripts"]
  gulp.watch "./components/**/**/**/*.styl", ["styles"]

gulp.task "default", ["views", "scripts", "styles", "watch"]
